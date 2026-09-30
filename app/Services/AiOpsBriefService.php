<?php

namespace App\Services;

use App\Models\AdminActivity;
use App\Models\AiOpsBrief;
use App\Models\GeneratedContent;
use App\Models\SystemSetting;
use App\Models\Template;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class AiOpsBriefService
{
    /**
     * Generate and store an ops brief for the last 7 days.
     */
    public function generate(string $trigger = 'scheduled'): AiOpsBrief
    {
        $periodEnd = Carbon::now();
        $periodStart = Carbon::now()->subDays(7);

        $metrics = $this->collectMetrics($periodStart, $periodEnd);

        $brief = AiOpsBrief::create([
            'period_start' => $periodStart->toDateString(),
            'period_end' => $periodEnd->toDateString(),
            'metrics' => $metrics,
            'trigger' => $trigger,
            'status' => 'failed',
            'summary' => null,
        ]);

        try {
            $model = 'gpt-4';
            $summary = $this->askOpenAi($metrics, $model);

            $brief->update([
                'summary' => $summary,
                'status' => 'success',
                'model' => $model,
                'error_message' => null,
            ]);
        } catch (\Throwable $e) {
            Log::error('Weekly AI ops brief OpenAI call failed', [
                'error' => $e->getMessage(),
            ]);

            $brief->update([
                'summary' => $this->buildFallbackSummary($metrics),
                'status' => 'fallback',
                'model' => null,
                'error_message' => $e->getMessage(),
            ]);
        }

        return $brief->fresh();
    }

    /**
     * Skip scheduled runs when a successful/fallback brief already exists this week.
     */
    public function hasRecentBrief(int $withinDays = 6): bool
    {
        return AiOpsBrief::where('created_at', '>=', Carbon::now()->subDays($withinDays))
            ->whereIn('status', ['success', 'fallback'])
            ->exists();
    }

    public function collectMetrics(Carbon $periodStart, Carbon $periodEnd): array
    {
        $previousStart = $periodStart->copy()->subDays(7);

        $documentsThisPeriod = GeneratedContent::whereBetween('created_at', [$periodStart, $periodEnd])->count();
        $documentsPreviousPeriod = GeneratedContent::whereBetween('created_at', [$previousStart, $periodStart])->count();

        $changePercent = $documentsPreviousPeriod > 0
            ? round((($documentsThisPeriod - $documentsPreviousPeriod) / $documentsPreviousPeriod) * 100, 1)
            : ($documentsThisPeriod > 0 ? 100 : 0);

        $topTemplates = Template::withCount(['generatedContents as period_count' => function ($query) use ($periodStart, $periodEnd) {
            $query->whereBetween('created_at', [$periodStart, $periodEnd]);
        }])
            ->orderByDesc('period_count')
            ->limit(5)
            ->get()
            ->filter(fn ($template) => $template->period_count > 0)
            ->map(fn ($template) => [
                'title' => $template->title,
                'category' => $template->category,
                'count' => (int) $template->period_count,
            ])
            ->values()
            ->all();

        $documentsByRole = GeneratedContent::query()
            ->join('users', 'users.id', '=', 'generated_contents.user_id')
            ->whereBetween('generated_contents.created_at', [$periodStart, $periodEnd])
            ->selectRaw('users.role, COUNT(*) as total')
            ->groupBy('users.role')
            ->pluck('total', 'users.role')
            ->map(fn ($total) => (int) $total)
            ->all();

        $activeUserIds = GeneratedContent::whereBetween('created_at', [$periodStart, $periodEnd])
            ->distinct()
            ->pluck('user_id');

        return [
            'period_start' => $periodStart->toDateString(),
            'period_end' => $periodEnd->toDateString(),
            'documents_generated' => $documentsThisPeriod,
            'documents_previous_period' => $documentsPreviousPeriod,
            'document_change_percent' => $changePercent,
            'total_words' => (int) GeneratedContent::whereBetween('created_at', [$periodStart, $periodEnd])->sum('word_count'),
            'active_users' => $activeUserIds->count(),
            'new_users' => User::whereBetween('created_at', [$periodStart, $periodEnd])->count(),
            'new_templates' => Template::whereBetween('created_at', [$periodStart, $periodEnd])->count(),
            'admin_activities' => AdminActivity::whereBetween('created_at', [$periodStart, $periodEnd])->count(),
            'top_templates' => $topTemplates,
            'documents_by_role' => $documentsByRole,
        ];
    }

    public function buildFallbackSummary(array $metrics): string
    {
        $change = $metrics['document_change_percent'];
        $direction = $change > 0 ? "up {$change}%" : ($change < 0 ? 'down '.abs($change).'%' : 'unchanged');

        $top = 'None in this period.';
        if (!empty($metrics['top_templates'])) {
            $first = $metrics['top_templates'][0];
            $top = "{$first['title']} ({$first['count']} uses)";
        }

        $roles = $metrics['documents_by_role'] ?: [];
        $roleText = empty($roles)
            ? 'no role breakdown'
            : collect($roles)->map(fn ($count, $role) => "{$role}: {$count}")->implode(', ');

        return implode("\n", [
            "- Generated documents: {$metrics['documents_generated']} ({$direction} vs previous 7 days).",
            "- Active users who generated content: {$metrics['active_users']}. New accounts: {$metrics['new_users']}.",
            "- Most used template: {$top}.",
            "- Documents by role: {$roleText}.",
            "- Admin activities logged: {$metrics['admin_activities']}. New templates: {$metrics['new_templates']}.",
            '- Action: review unused templates and follow up with inactive lecturers/students if usage is low.',
            '(Numbers-only summary. OpenAI was unavailable.)',
        ]);
    }

    private function askOpenAi(array $metrics, string $model): string
    {
        $metricsJson = json_encode($metrics, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $response = OpenAI::chat()->create([
            'model' => $model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an operations analyst for a university AI writing assistant. Write a concise weekly brief for the SuperAdmin. Use only the provided metrics. Do not invent numbers. Output 5 to 7 short bullet points in English. End with 1 practical action.',
                ],
                [
                    'role' => 'user',
                    'content' => "Write this week's AI operations brief from these metrics:\n\n{$metricsJson}",
                ],
            ],
            'max_tokens' => 700,
            'temperature' => 0.4,
        ]);

        $content = trim($response->choices[0]->message->content ?? '');

        if ($content === '') {
            throw new \RuntimeException('OpenAI returned an empty brief.');
        }

        return $content;
    }

    public static function isEnabled(): bool
    {
        return SystemSetting::get('ai_ops_brief_enabled', true);
    }
}
