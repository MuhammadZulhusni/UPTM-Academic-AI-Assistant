<?php

namespace Tests\Unit;

use App\Models\Template;
use App\Services\TemplateSuggestionService;
use Illuminate\Support\Collection;
use Tests\TestCase;

class TemplateSuggestionMatchTest extends TestCase
{
    public function test_only_known_templates_are_returned_with_a_reason(): void
    {
        $templates = new Collection([
            new Template(['id' => 4, 'title' => 'Reflection', 'description' => 'Write a reflection', 'category' => 'Student']),
            new Template(['id' => 9, 'title' => 'Lesson plan', 'description' => 'Plan a class', 'category' => 'Lecturer']),
        ]);

        $json = json_encode([
            'matches' => [
                ['id' => 4, 'reason' => 'This fits a reflection task.'],
                ['id' => 99, 'reason' => 'Invented template.'],
                ['id' => 9, 'reason' => ''],
            ],
        ]);

        $matches = (new TemplateSuggestionService())->mapMatches($json, $templates);

        $this->assertCount(1, $matches);
        $this->assertSame(4, $matches[0]['id']);
        $this->assertSame('Reflection', $matches[0]['title']);
        $this->assertSame('This fits a reflection task.', $matches[0]['reason']);
    }

    public function test_invalid_json_returns_no_matches(): void
    {
        $matches = (new TemplateSuggestionService())->mapMatches('not json', new Collection());

        $this->assertSame([], $matches);
    }
}
