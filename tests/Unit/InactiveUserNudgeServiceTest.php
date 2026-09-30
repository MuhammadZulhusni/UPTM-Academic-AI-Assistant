<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\InactiveUserNudgeService;
use Tests\TestCase;

class InactiveUserNudgeServiceTest extends TestCase
{
    public function test_fallback_email_uses_inactivity_days_without_inventing_usage(): void
    {
        $user = new User([
            'name' => 'Aina',
            'role' => 'student',
        ]);

        $copy = (new InactiveUserNudgeService())->buildFallbackEmail($user, 21);

        $this->assertStringContainsString('21 days', $copy['body']);
        $this->assertStringContainsString('Templates', $copy['body']);
        $this->assertNotEmpty($copy['subject']);
    }

    public function test_fallback_email_marks_same_day_as_test_reminder(): void
    {
        $user = new User([
            'name' => 'Aina',
            'role' => 'student',
        ]);

        $copy = (new InactiveUserNudgeService())->buildFallbackEmail($user, 0);

        $this->assertStringContainsString('test reminder', $copy['body']);
    }
}
