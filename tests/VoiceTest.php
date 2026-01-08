<?php
use PHPUnit\Framework\TestCase;
use NeuraPHP\Modules\Voice;

class VoiceTest extends TestCase
{
    public function testGenerateVoice()
    {
        $voice = new Voice();
        $file = $voice->generate('test text', __DIR__ . '/tmp_storage');
        $this->assertFileExists($file);
        unlink($file);
    }
}
