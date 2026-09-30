<?php

namespace App\Services;

use App\Mail\InactiveUserNudgeMail;
use App\Models\InactiveUserNudge;
use App\Models\SystemSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use OpenAI\Laravel\Facades\OpenAI;

class InactiveUserNudgeService
{
    public function run(string $trigger = 'scheduled', ?int $idleDays = null, int $limit = 10): array
    {
        $idleDays = $idleDays ?? $this->idleDays();
        $users = $this->eligibleUsers($idleDays, $limit);
        $sent = 0;
        $failed = 0;

        foreach ($users as $user) {
            $daysInactive = $this->daysInactive($user);
            $copy = $this->writeEmail($user, $daysInactive);

            try {
                Mail::to($user->email)->send(new InactiveUserNudgeMail(
                    $user->name,
                    $copy['subject'],
                    $copy['body'],
                ));

                InactiveUserNudge::create([
                    'user_id' => $user->id,
                    'days_inactive' => $daysInactive,
                    'subject' => $copy['subject'],
                    'body' => $copy['body'],
                    'status' => 'sent',
                    'trigger' => $trigger,
                    'model' => $copy['model'],
                    'error_message' => $copy['error'],
                    'sent_at' => now(),
                ]);

                $sent++;
            } catch (\Throwable $e) {
                Log::error('Inactive user nudge email failed', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);

                InactiveUserNudge::create([
                    'user_id' => $user->id,
                    'days_inactive' => $daysInactive,
                    'subject' => $copy['subject'],
                    'body' => $copy['body'],
                    'status' => 'failed',
                    'trigger' => $trigger,
                    'model' => $copy['model'],
                    'error_message' => $e->getMessage(),
                    'sent_at' => null,
                ]);

                $failed++;
            }
        }

        return [
            'eligible' => $users->count(),
            'sent' => $sent,
            'failed' => $failed,
        ];
    }

    public function eligibleUsers(int $idleDays = 14, int $limit = 10): Collection
    {
        $idleSince = Carbon::now()->subDays($idleDays);

        return User::query()
            ->whereIn('role', ['student', 'lecturer'])
            ->where('status', '1')
            ->where('is_active', true)
            ->where('created_at', '<=', $idleSince)
            ->whereDoesntHave('generatedContents', function ($query) use ($idleSince) {
                $query->where('created_at', '>=', $idleSince);
            })
            ->whereDoesntHave('inactiveNudges', function ($query) use ($idleSince) {
                $query->where('status', 'sent')
                    ->where('created_at', '>=', $idleSince);
            })
            ->orderBy('created_at')
            ->limit($limit)
            ->get();
    }

    public function buildFallbackEmail(User $user, int $daysInactive): array
    {
        $activity = $daysInactive < 1
            ? 'This is a test reminder from SuperAdmin.'
            : "You have not generated a document in {$daysInactive} days.";

        return [
            'subject' => 'A reminder from UPTM Academic AI Assistant',
            'body' => "{$activity} Templates for your role are still available when you next need academic writing support.",
            'model' => null,
            'error' => null,
        ];
    }

    public function preview(User $user): array
    {
        return $this->writeEmail($user, $this->daysInactive($user));
    }

    public function sendToUser(User $user, string $trigger = 'manual'): InactiveUserNudge
    {
        $daysInactive = $this->daysInactive($user);
        $copy = $this->writeEmail($user, $daysInactive);

        try {
            Mail::to($user->email)->send(new InactiveUserNudgeMail(
                $user->name,
                $copy['subject'],
                $copy['body'],
            ));

            return InactiveUserNudge::create([
                'user_id' => $user->id,
                'days_inactive' => $daysInactive,
                'subject' => $copy['subject'],
                'body' => $copy['body'],
                'status' => 'sent',
                'trigger' => $trigger,
                'model' => $copy['model'],
                'error_message' => $copy['error'],
                'sent_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Inactive user nudge send failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return InactiveUserNudge::create([
                'user_id' => $user->id,
                'days_inactive' => $daysInactive,
                'subject' => $copy['subject'],
                'body' => $copy['body'],
                'status' => 'failed',
                'trigger' => $trigger,
                'model' => $copy['model'],
                'error_message' => $e->getMessage(),
                'sent_at' => null,
            ]);
        }
    }

    public function sendPreviewToAddress(User $user, string $email): array
    {
        $copy = $this->preview($user);

        Mail::to($email)->send(new InactiveUserNudgeMail(
            $user->name,
            $copy['subject'],
            $copy['body'],
        ));

        return $copy;
    }

    public function idleDays(): int
    {
        return SystemSetting::getInactiveUserNudgeIdleDays();
    }

    public static function isEnabled(): bool
    {
        return SystemSetting::get('inactive_user_nudge_enabled', true);
    }

    public function daysInactive(User $user): int
    {
        $lastGenerated = $user->generatedContents()->latest('created_at')->value('created_at');

        if (!$lastGenerated) {
            return max(0, (int) $user->created_at->diffInDays(now()));
        }

        return (int) Carbon::parse($lastGenerated)->diffInDays(now());
    }

    private function writeEmail(User $user, int $daysInactive): array
    {
        $fallback = $this->buildFallbackEmail($user, $daysInactive);

        try {
            $response = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Write a short polite reminder email for a university academic writing assistant. Return JSON only with keys subject and body. Do not start with Hello, Hi, or Dear — the template already greets the student. Body max 80 words. Do not invent usage numbers beyond the facts given. Do not mention a login hint; the email already has a Sign in button.',
                    ],
                    [
                        'role' => 'user',
                        'content' => json_encode([
                            'name' => $user->name,
                            'role' => $user->role,
                            'days_inactive' => $daysInactive,
                            'login_hint' => 'Sign in to UPTM Academic AI Assistant and open Templates.',
                        ]),
                    ],
                ],
                'max_tokens' => 220,
                'temperature' => 0.4,
            ]);

            $parsed = $this->parseCopy(trim($response->choices[0]->message->content ?? ''));

            if ($parsed === null) {
                throw new \RuntimeException('OpenAI returned invalid reminder copy.');
            }

            return [
                'subject' => $parsed['subject'],
                'body' => $parsed['body'],
                'model' => 'gpt-3.5-turbo',
                'error' => null,
            ];
        } catch (\Throwable $e) {
            Log::warning('Inactive user nudge OpenAI failed, using fallback copy', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            $fallback['error'] = $e->getMessage();

            return $fallback;
        }
    }

    private function parseCopy(string $content): ?array
    {
        $json = preg_replace('/^```(?:json)?\s*|\s*```$/', '', $content) ?? $content;
        $data = json_decode($json, true);

        if (!is_array($data) || empty($data['subject']) || empty($data['body'])) {
            return null;
        }

        return [
            'subject' => trim((string) $data['subject']),
            'body' => trim((string) $data['body']),
        ];
    }
}
