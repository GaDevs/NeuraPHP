<?php
use PHPUnit\Framework\TestCase;
use NeuraPHP\Modules\Image;

class ImageTest extends TestCase
{
    public function testGenerateImage()
    {
        $image = new Image();
        $file = $image->generate('test prompt', __DIR__ . '/tmp_storage');
        $this->assertFileExists($file);
        unlink($file);
    }
}
