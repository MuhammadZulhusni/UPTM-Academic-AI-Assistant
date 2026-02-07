<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Template;

class TemplateUnitTest extends TestCase
{

    // TC01 Valid: single placeholder replacement
    public function test_TC01_generateContent_replaces_single_placeholder()
    {
        $template = new Template();
        $template->prompt = 'Hello {name}';

        $result = $template->generateContent(['name' => 'Student']);

        $this->assertEquals('Hello Student', $result);
    }

    // TC02 Valid: multiple placeholders replacement
    public function test_TC02_generateContent_replaces_multiple_placeholders()
    {
        $template = new Template();
        $template->prompt = 'Hi {name}, welcome to {school}!';

        $result = $template->generateContent([
            'name' => 'Ali',
            'school' => 'UPTM'
        ]);

        $this->assertEquals('Hi Ali, welcome to UPTM!', $result);
    }

    // TC03 Invalid: missing placeholder key
    public function test_TC03_generateContent_does_not_replace_when_data_missing()
    {
        $template = new Template();
        $template->prompt = 'Hello {name}';

        $result = $template->generateContent([]);

        $this->assertEquals('Hello {name}', $result);
    }

    // TC04 Invalid: wrong key provided
    public function test_TC04_generateContent_does_not_replace_with_wrong_key()
    {
        $template = new Template();
        $template->prompt = 'Hello {name}';

        $result = $template->generateContent(['wrongkey' => 'Student']);

        $this->assertEquals('Hello {name}', $result);
    }

    // TC05 Boundary: empty string value replacement
    public function test_TC05_generateContent_replaces_with_empty_value()
    {
        $template = new Template();
        $template->prompt = 'Hello {name}';

        $result = $template->generateContent(['name' => '']);

        $this->assertEquals('Hello ', $result);
    }

    // TC06 Boundary: long input value
    public function test_TC06_generateContent_handles_long_input_value()
    {
        $template = new Template();
        $template->prompt = 'Hello {name}';

        $longName = str_repeat("A", 100);

        $result = $template->generateContent(['name' => $longName]);

        $this->assertEquals('Hello ' . $longName, $result);
    }

    // TC07 Boundary: prompt without placeholder
    public function test_TC07_generateContent_returns_same_prompt_if_no_placeholder()
    {
        $template = new Template();
        $template->prompt = 'Hello Student';

        $result = $template->generateContent(['name' => 'Ali']);

        $this->assertEquals('Hello Student', $result);
    }
}
