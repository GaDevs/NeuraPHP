<?php
use PHPUnit\Framework\TestCase;

class XssTest extends TestCase
{
    public function testInputSanitization()
    {
        $input = "<script>alert('xss')</script>";
        $sanitized = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        $this->assertStringNotContainsString('<script>', $sanitized);
    }
}
