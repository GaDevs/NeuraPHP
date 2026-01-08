<?php
/**
 * NeuraPHP AI Engine™
 * Voice Generation Module (Stage 3)
 *
 * @package NeuraPHP\Modules
 * @author  NeuraPHP Team
 * @license MIT
 */

namespace NeuraPHP\Modules;

use NeuraPHP\Core\ProviderInterface;

class Voice
{
    /**
     * @var ProviderInterface|null
     */
    protected ?ProviderInterface $provider = null;

    /**
     * Voice constructor
     * @param ProviderInterface|null $provider
     */
    public function __construct(?ProviderInterface $provider = null)
    {
        $this->provider = $provider;
    }

    /**
     * Generate a voice file from text using provider
     * @param string $text
     * @return mixed
     */
    public function generate(string $text, ?string $dir = null): string
    {
        if ($this->provider) {
            return $this->provider->voice($text);
        }
        // Mock: cria arquivo de áudio fake
        $dir = $dir ?? sys_get_temp_dir();
        if (!is_dir($dir)) { mkdir($dir, 0777, true); }
        $file = $dir . '/test_voice_' . uniqid() . '.mp3';
        file_put_contents($file, 'FAKE_VOICE_DATA');
        return $file;
    }
}
