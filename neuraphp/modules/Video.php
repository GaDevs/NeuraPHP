<?php
/**
 * NeuraPHP AI Engine™
 * Video & Shorts Module (Stage 5)
 *
 * @package NeuraPHP\Modules
 * @author  NeuraPHP Team
 * @license MIT
 */

namespace NeuraPHP\Modules;

use NeuraPHP\Core\ProviderInterface;

class Video
{
    /**
     * @var ProviderInterface|null
     */
    protected ?ProviderInterface $provider = null;

    /**
     * Video constructor
     * @param ProviderInterface|null $provider
     */
    public function __construct(?ProviderInterface $provider = null)
    {
        $this->provider = $provider;
    }

    /**
     * Generate a video/short using provider
     * @param string $prompt
     * @return mixed
     */
    public function generateShort(string $prompt, ?string $dir = null): array
    {
        if ($this->provider) {
            return $this->provider->video($prompt);
        }
        // Mock: cria arquivo de vídeo fake e dados
        $dir = $dir ?? sys_get_temp_dir();
        if (!is_dir($dir)) { mkdir($dir, 0777, true); }
        $file = $dir . '/test_video_' . uniqid() . '.mp4';
        file_put_contents($file, 'FAKE_VIDEO_DATA');
        return [
            'file' => $file,
            'slides' => ['Slide 1', 'Slide 2'],
            'narration' => 'Fake narration for ' . $prompt
        ];
    }
}
