<?php

namespace jeremykenedy\LaravelPhpInfo\Tests;

use jeremykenedy\LaravelPhpInfo\PhpInfo;

class PhpInfoTest extends TestCase
{
    public function test_html_output_contains_only_the_body()
    {
        $result = (new PhpInfo)->format(file_get_contents(__DIR__.'/fixtures/phpinfo.html'));

        $this->assertTrue($result['html']);
        $this->assertTrue($result['available']);
        $this->containsText('memory_limit', $result['output']);
        $this->excludesText('<body', $result['output']);
        $this->excludesText('<style', $result['output']);
    }

    public function test_plain_text_output_is_not_treated_as_html()
    {
        $text = 'PHP Version 8.5.10 <script>alert(1)</script>';
        $result = (new PhpInfo)->format($text);

        $this->assertFalse($result['html']);
        $this->assertSame($text, $result['output']);
    }

    public function test_capture_restores_the_output_buffer()
    {
        $level = ob_get_level();
        $result = (new PhpInfo)->capture();

        $this->assertSame($level, ob_get_level());
        $this->assertTrue($result['available']);
        $this->containsText('PHP Version', $result['output']);
    }
}
