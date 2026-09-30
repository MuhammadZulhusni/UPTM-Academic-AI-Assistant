<?php

namespace App\Services;

use App\Models\Template;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class TemplateSuggestionService
{
    /**
     * Suggest new academic templates that do not duplicate existing ones.
     *
     * @return array<int, array<string, mixed>>
     */
    public function suggest(): array
    {
        $existing = Template::query()
            ->select('title', 'description', 'category')
            ->orderBy('title')
            ->limit(40)
            ->get()
            ->map(fn (Template $template) => [
                'title' => $template->title,
                'description' => $template->description,
                'category' => $template->category,
            ])
            ->all();

        $existingJson = json_encode($existing, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You help a university build AI writing templates for students and lecturers. Reply with JSON only.',
                ],
                [
                    'role' => 'user',
                    'content' => <<<PROMPT
Existing templates:
{$existingJson}

Suggest exactly 4 NEW academic templates that fill gaps. Do not copy existing titles.

Return JSON in this exact shape:
{
  "suggestions": [
    {
      "title": "short template name",
      "description": "one sentence about what it does",
      "category": "Student or Lecturer",
      "icon": "writing.png or teaching.png or learning.png",
      "reason": "one short sentence why this is useful given current templates",
      "input_fields": [
        {"title": "topic", "description": "What the user should type"}
      ],
      "prompt": "Full custom prompt. Use {variable} placeholders that match input_fields titles. Academic UPTM style."
    }
  ]
}

Rules:
- category must be Student or Lecturer
- icon must be writing.png, teaching.png, or learning.png
- 1 input_fields item only
- input field title MUST be a short variable name only (max 30 characters), like topic or course_name. No sentences, no spaces.
- prompt must include that {variable}
PROMPT,
                ],
            ],
            'max_tokens' => 1800,
            'temperature' => 0.6,
        ]);

        $content = trim($response->choices[0]->message->content ?? '');
        $content = preg_replace('/^```(?:json)?\s*|\s*```$/', '', $content) ?? $content;
        $decoded = json_decode($content, true);

        $suggestions = $decoded['suggestions'] ?? [];
        if (!is_array($suggestions) || $suggestions === []) {
            throw new \RuntimeException('AI did not return template suggestions.');
        }

        return collect($suggestions)
            ->take(4)
            ->map(fn ($item) => $this->normalizeSuggestion(is_array($item) ? $item : []))
            ->filter()
            ->values()
            ->all();
    }

    private function normalizeSuggestion(array $item): ?array
    {
        $title = trim((string) ($item['title'] ?? ''));
        $prompt = trim((string) ($item['prompt'] ?? ''));
        if ($title === '' || $prompt === '') {
            return null;
        }

        $category = ($item['category'] ?? '') === 'Lecturer' ? 'Lecturer' : 'Student';
        $icon = in_array($item['icon'] ?? '', ['writing.png', 'teaching.png', 'learning.png'], true)
            ? $item['icon']
            : 'writing.png';

        $fields = collect($item['input_fields'] ?? [])
            ->filter(fn ($field) => is_array($field) && trim((string) ($field['title'] ?? '')) !== '')
            ->map(function ($field) {
                $raw = strtolower(trim((string) $field['title']));
                $fieldTitle = preg_replace('/[^a-z0-9_]/', '', str_replace(' ', '_', $raw));
                $fieldTitle = substr($fieldTitle !== '' ? $fieldTitle : 'topic', 0, 30);

                return [
                    'title' => $fieldTitle,
                    'description' => trim((string) ($field['description'] ?? 'Enter details for this field')),
                ];
            })
            ->take(1)
            ->values()
            ->all();

        if ($fields === []) {
            $fields = [
                ['title' => 'topic', 'description' => 'What specific subject should the content cover?'],
            ];
        }

        return [
            'title' => $title,
            'description' => trim((string) ($item['description'] ?? '')),
            'category' => $category,
            'icon' => $icon,
            'reason' => trim((string) ($item['reason'] ?? '')),
            'input_fields' => $fields,
            'prompt' => $prompt,
        ];
    }

    /**
     * Write a custom prompt from the user's description and the fields already on the form.
     *
     * @param  array<int, array<string, mixed>>  $fields
     */
    public function writePrompt(string $wish, string $title, string $description, string $category, array $fields): string
    {
        $variables = [];
        $fieldLines = [];
        foreach ($fields as $field) {
            if (!is_array($field)) {
                continue;
            }
            $name = trim((string) ($field['title'] ?? ''));
            if ($name === '' || in_array($name, $variables, true)) {
                continue;
            }
            $variables[] = $name;
            $helper = trim((string) ($field['description'] ?? ''));
            $fieldLines[] = '- {'.$name.'}'.($helper !== '' ? ' — '.$helper : '');
        }

        if ($variables === []) {
            throw new \RuntimeException('Add a variable name before writing the prompt.');
        }

        $fieldsText = implode("\n", $fieldLines);
        $title = trim($title) !== '' ? trim($title) : 'Untitled template';
        $description = trim($description);
        $audience = in_array($category, ['Student', 'Lecturer'], true) ? $category : 'students or lecturers';

        try {
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You write custom prompts for a university AI writing tool. Reply with the prompt text only. No markdown fences and no JSON.',
                    ],
                    [
                        'role' => 'user',
                        'content' => <<<PROMPT
Template name: {$title}
Description: {$description}
Audience: {$audience}

The user wants the prompt to work like this:
{$wish}

Variables that must appear exactly, including the braces:
{$fieldsText}

Write one custom prompt the tool will send to the model later.
- Start with a role suited to the audience.
- Follow the user's request above.
- Include every listed variable exactly once, braces included.
- Set the output format, headings, and tone.
- Do not invent extra variables.
PROMPT,
                    ],
                ],
                'max_tokens' => 800,
                'temperature' => 0.4,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Custom prompt write failed', [
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException('The prompt could not be written. Try again.');
        }

        $content = trim($response->choices[0]->message->content ?? '');

        return $this->finishPrompt($content, $variables);
    }

    /**
     * @param  array<int, string>  $variables
     */
    public function finishPrompt(string $content, array $variables): string
    {
        $prompt = trim($content);
        $prompt = preg_replace('/^```(?:\w+)?\s*|\s*```$/', '', $prompt) ?? $prompt;
        $prompt = trim($prompt);

        $decoded = json_decode($prompt, true);
        if (is_array($decoded) && isset($decoded['prompt'])) {
            $prompt = trim((string) $decoded['prompt']);
        }

        if ($prompt === '') {
            throw new \RuntimeException('AI did not return a prompt.');
        }

        foreach ($variables as $name) {
            $name = trim($name);
            if ($name !== '' && !str_contains($prompt, '{'.$name.'}')) {
                $prompt .= "\n- ".$name.': {'.$name.'}';
            }
        }

        return $prompt;
    }

    /**
     * Pick existing templates that fit what the user is looking for.
     *
     * @param  Collection<int, Template>  $templates
     * @return array<int, array<string, mixed>>
     */
    public function matchNeed(string $need, Collection $templates): array
    {
        if ($templates->isEmpty()) {
            return [];
        }

        $catalog = $templates->map(fn (Template $template) => [
            'id' => $template->id,
            'title' => $template->title,
            'description' => $template->description,
            'category' => $template->category,
        ])->values()->all();

        $catalogJson = json_encode($catalog, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        try {
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You match a user to existing university writing templates. Reply with JSON only. Never invent a template id.',
                    ],
                    [
                        'role' => 'user',
                        'content' => <<<PROMPT
The user is looking for:
{$need}

Templates:
{$catalogJson}

Choose up to 3 existing templates that fit. Order the best fit first. If none fit, return an empty list.

Return JSON in this exact shape:
{"matches":[{"id":1,"reason":"one short sentence why this template fits"}]}
PROMPT,
                    ],
                ],
                'max_tokens' => 600,
                'temperature' => 0.2,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Template match failed', [
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException('Suggestions are unavailable right now. Try again.');
        }

        $content = trim($response->choices[0]->message->content ?? '');

        return $this->mapMatches($content, $templates);
    }

    /**
     * @param  Collection<int, Template>  $templates
     * @return array<int, array<string, mixed>>
     */
    public function mapMatches(string $content, Collection $templates): array
    {
        $content = trim($content);
        $content = preg_replace('/^```(?:json)?\s*|\s*```$/', '', $content) ?? $content;
        $decoded = json_decode($content, true);
        $raw = is_array($decoded) ? ($decoded['matches'] ?? []) : [];

        if (!is_array($raw)) {
            return [];
        }

        $byId = $templates->keyBy('id');

        return collect($raw)
            ->filter(fn ($item) => is_array($item))
            ->map(function (array $item) use ($byId) {
                $template = $byId->get((int) ($item['id'] ?? 0));
                $reason = trim((string) ($item['reason'] ?? ''));
                if (!$template || $reason === '') {
                    return null;
                }

                return [
                    'id' => $template->id,
                    'title' => $template->title,
                    'description' => $template->description,
                    'category' => $template->category,
                    'reason' => mb_substr($reason, 0, 300),
                ];
            })
            ->filter()
            ->unique('id')
            ->take(3)
            ->values()
            ->all();
    }
}
