<?php
use PHPUnit\Framework\TestCase;

class FileTraversalTest extends TestCase
{
    public function testFileTraversalPrevention()
    {
        $input = '../etc/passwd';
        $safe = basename($input);
        $this->assertEquals('passwd', $safe);
    }
}
