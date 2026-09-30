<?php

namespace Tests\Unit;

use App\Models\Template;
use App\Services\AssignmentBriefExtractor;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AssignmentBriefExtractorTest extends TestCase
{
    public function test_generation_attaches_the_pdf_and_returns_the_written_output(): void
    {
        config(['openai.api_key' => 'test-key', 'openai.base_uri' => 'https://api.openai.com/v1']);

        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'A completed essay about climate change.']],
                ],
            ]),
        ]);

        $file = UploadedFile::fake()->create('soalan.pdf', 20, 'application/pdf');
        $output = (new AssignmentBriefExtractor())->generateFromBrief($file, [
            ['role' => 'system', 'content' => 'Reply in English.'],
            ['role' => 'user', 'content' => 'Write an essay about {topic}.'],
        ], 'gpt-4');

        $this->assertSame('A completed essay about climate change.', $output);

        Http::assertSent(function ($request) {
            $messages = $request->data()['messages'] ?? [];
            $user = $messages[1]['content'] ?? [];
            $attachment = $user[1] ?? [];

            return ($request->data()['model'] ?? null) === 'gpt-4o'
                && ($attachment['type'] ?? null) === 'file'
                && str_contains($user[0]['text'] ?? '', 'Base the whole response on that file');
        });
    }

    public function test_image_is_sent_as_an_image_url(): void
    {
        $file = UploadedFile::fake()->image('nota.jpg');
        $part = (new AssignmentBriefExtractor())->attachmentPart($file);

        $this->assertSame('image_url', $part['type']);
        $this->assertStringStartsWith('data:image/', $part['image_url']['url']);
    }

    public function test_upload_box_submits_the_file_with_generate(): void
    {
        $enabled = new Template(['allow_brief_upload' => true]);
        $html = view('components.assignment_brief_upload', [
            'template' => $enabled,
        ])->render();

        $this->assertStringContainsString('name="brief"', $html);
        $this->assertStringContainsString('written from that file', $html);
        $this->assertStringNotContainsString('Fill fields from file', $html);

        $disabled = new Template(['allow_brief_upload' => false]);
        $empty = view('components.assignment_brief_upload', [
            'template' => $disabled,
        ])->render();

        $this->assertStringNotContainsString('name="brief"', $empty);
    }
}
