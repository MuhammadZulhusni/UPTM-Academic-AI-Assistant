<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AssignmentBriefExtractor
{
    /**
     * Write the template response from the attached photo or PDF.
     *
     * @param  array<int, array<string, mixed>>  $messages
     */
    public function generateFromBrief(UploadedFile $file, array $messages, string $requestedModel): string
    {
        $apiKey = config('openai.api_key');
        if (!is_string($apiKey) || $apiKey === '') {
            throw new \RuntimeException('Assignment upload is unavailable because the AI key is not configured.');
        }

        set_time_limit(120);

        $model = $requestedModel === 'gpt-4' ? 'gpt-4o' : 'gpt-4o-mini';
        $base = rtrim((string) (config('openai.base_uri') ?: 'https://api.openai.com/v1'), '/');

        try {
            $response = Http::withToken($apiKey)
                ->timeout(110)
                ->acceptJson()
                ->post($base.'/chat/completions', [
                    'model' => $model,
                    'temperature' => 0.7,
                    'max_tokens' => $model === 'gpt-4o' ? 4096 : 3000,
                    'messages' => $this->withAttachment($messages, $file),
                ])
                ->throw();
        } catch (RequestException $e) {
            Log::warning('Assignment brief generation failed', [
                'status' => $e->response?->status(),
                'body' => mb_substr((string) $e->response?->body(), 0, 400),
            ]);

            throw new \RuntimeException('The file could not be read. Use a clearer JPG, PNG, WEBP, or PDF, then try again.');
        }

        $content = trim((string) data_get($response->json(), 'choices.0.message.content', ''));
        if ($content === '') {
            throw new \RuntimeException('The file was read, but no content was generated. Try again.');
        }

        return $content;
    }

    /**
     * @param  array<int, array<string, mixed>>  $messages
     * @return array<int, array<string, mixed>>
     */
    public function withAttachment(array $messages, UploadedFile $file): array
    {
        $prepared = [];

        foreach ($messages as $message) {
            if (($message['role'] ?? '') === 'user' && is_string($message['content'] ?? null)) {
                $prepared[] = [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $message['content']."\n\nThe assignment brief is attached. Base the whole response on that file or image. Do not ask for it to be typed again.",
                        ],
                        $this->attachmentPart($file),
                    ],
                ];
                continue;
            }

            $prepared[] = $message;
        }

        return $prepared;
    }

    /**
     * @return array<string, mixed>
     */
    public function attachmentPart(UploadedFile $file): array
    {
        $binary = base64_encode((string) file_get_contents($file->getRealPath()));
        $extension = strtolower($file->getClientOriginalExtension());
        $isPdf = $extension === 'pdf' || $file->getMimeType() === 'application/pdf';

        if ($isPdf) {
            return [
                'type' => 'file',
                'file' => [
                    'filename' => $file->getClientOriginalName() ?: 'brief.pdf',
                    'file_data' => 'data:application/pdf;base64,'.$binary,
                ],
            ];
        }

        $mime = $file->getMimeType() ?: 'image/jpeg';
        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            $mime = match ($extension) {
                'png' => 'image/png',
                'webp' => 'image/webp',
                default => 'image/jpeg',
            };
        }

        return [
            'type' => 'image_url',
            'image_url' => [
                'url' => 'data:'.$mime.';base64,'.$binary,
            ],
        ];
    }
}
