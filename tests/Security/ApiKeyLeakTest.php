<?php
use PHPUnit\Framework\TestCase;

class ApiKeyLeakTest extends TestCase
{
    public function testNoApiKeyInLogs()
    {
        $log = file_get_contents(__DIR__ . '/../../neuraphp/logs/app.log');
        $this->assertStringNotContainsString('sk-', $log);
    }
}
