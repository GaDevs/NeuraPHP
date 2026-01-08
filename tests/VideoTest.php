<?php
use PHPUnit\Framework\TestCase;
use NeuraPHP\Modules\Video;

class VideoTest extends TestCase
{
    public function testGenerateShort()
    {
        $video = new Video();
        $result = $video->generateShort('php', __DIR__ . '/tmp_storage');
        $this->assertArrayHasKey('file', $result);
        $this->assertArrayHasKey('slides', $result);
        $this->assertArrayHasKey('narration', $result);
        $this->assertFileExists($result['file']);
        unlink($result['file']);
    }
}
