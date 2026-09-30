<?php

namespace Tests\Unit;

use App\Services\AiOpsBriefService;
use Tests\TestCase;

class AiOpsBriefServiceTest extends TestCase
{
    public function test_fallback_summary_uses_metrics_without_inventing_counts()
    {
        $service = new AiOpsBriefService();

        $summary = $service->buildFallbackSummary([
            'documents_generated' => 12,
            'document_change_percent' => 50,
            'active_users' => 4,
            'new_users' => 2,
            'admin_activities' => 7,
            'new_templates' => 1,
            'top_templates' => [
                ['title' => 'Essay Outline', 'count' => 8],
            ],
            'documents_by_role' => [
                'student' => 10,
                'lecturer' => 2,
            ],
        ]);

        $this->assertStringContainsString('12', $summary);
        $this->assertStringContainsString('Essay Outline', $summary);
        $this->assertStringContainsString('student: 10', $summary);
        $this->assertStringContainsString('OpenAI was unavailable', $summary);
    }
}
