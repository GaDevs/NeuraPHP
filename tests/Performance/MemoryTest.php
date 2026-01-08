<?php
use PHPUnit\Framework\TestCase;
use NeuraPHP\Core\Memory;

class MemoryTest extends TestCase
{
    public function testMemoryStress()
    {
        $memory = new Memory(__DIR__ . '/../../neuraphp/memory');
        $cid = 'perf_' . uniqid();
        for ($i = 0; $i < 100; $i++) {
            $memory->saveHistory($cid, [['role' => 'user', 'message' => 'msg' . $i, 'timestamp' => time()]]);
        }
        $history = $memory->getHistory($cid);
        $this->assertNotEmpty($history);
    }
}
