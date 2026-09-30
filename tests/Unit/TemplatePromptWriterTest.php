<?php

namespace Tests\Unit;

use App\Services\TemplateSuggestionService;
use RuntimeException;
use Tests\TestCase;

class TemplatePromptWriterTest extends TestCase
{
    public function test_missing_variable_is_added_to_the_prompt(): void
    {
        $prompt = (new TemplateSuggestionService())->finishPrompt(
            "Act as a lecturer.\nTeach {topic}.",
            ['topic', 'duration']
        );

        $this->assertStringContainsString('{topic}', $prompt);
        $this->assertStringContainsString('{duration}', $prompt);
    }

    public function test_json_wrapper_is_unwrapped(): void
    {
        $prompt = (new TemplateSuggestionService())->finishPrompt(
            '{"prompt":"Act as a lecturer and use {topic}."}',
            ['topic']
        );

        $this->assertSame('Act as a lecturer and use {topic}.', $prompt);
    }

    public function test_empty_prompt_is_rejected(): void
    {
        $this->expectException(RuntimeException::class);

        (new TemplateSuggestionService())->finishPrompt('   ', ['topic']);
    }
}
