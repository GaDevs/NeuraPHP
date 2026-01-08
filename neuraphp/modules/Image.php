<?php
/**
 * NeuraPHP AI Engine™
 * Image Generation Module (Stage 3)
 *
 * @package NeuraPHP\Modules
 * @author  NeuraPHP Team
 * @license MIT
 */

namespace NeuraPHP\Modules;

use NeuraPHP\Core\ProviderInterface;

class Image
{
    /**
     * @var ProviderInterface|null
     */
    protected ?ProviderInterface $provider = null;

    /**
     * Image constructor
     * @param ProviderInterface|null $provider
     */
    public function __construct(?ProviderInterface $provider = null)
    {
        $this->provider = $provider;
    }

    /**
     * Generate an image from a prompt using provider
     * @param string $prompt
     * @return mixed
     */
    public function generate(string $prompt, ?string $dir = null): string
    {
        if ($this->provider) {
            return $this->provider->image($prompt);
        }
        // Mock: cria arquivo de imagem fake
        $dir = $dir ?? sys_get_temp_dir();
        if (!is_dir($dir)) { mkdir($dir, 0777, true); }
        $file = $dir . '/test_image_' . uniqid() . '.png';
        file_put_contents($file, 'FAKE_IMAGE_DATA');
        return $file;
    }
}
