<?php
use PHPUnit\Framework\TestCase;
use NeuraPHP\Modules\Chat;
use NeuraPHP\Core\Memory;

class ChatTest extends TestCase
{
    public function testAddAndGetHistory()
    {
        $memoryDir = __DIR__ . '/tmp_memory';
        if (is_dir($memoryDir)) {
            array_map('unlink', glob($memoryDir . '/*.json'));
        }
        $memory = new Memory($memoryDir);
        $chat = new Chat($memory);
        $cid = 'testchat';
        $chat->addMessage($cid, 'user', 'Hello');
        $history = $chat->getHistory($cid);
        $this->assertCount(1, $history);
        $this->assertEquals('Hello', $history[0]['message']);
    }
}
