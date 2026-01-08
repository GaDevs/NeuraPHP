<?php
/**
 * NeuraPHP AI Engine™
 * SEO & Content Module (Stage 4)
 *
 * @package NeuraPHP\Modules
 * @author  NeuraPHP Team
 * @license MIT
 */

namespace NeuraPHP\Modules;

use NeuraPHP\Core\ProviderInterface;

class SEO
{
    /**
     * @var ProviderInterface|null
     */
    protected ?ProviderInterface $provider = null;

    /**
     * SEO constructor
     * @param ProviderInterface|null $provider
     */
    public function __construct(?ProviderInterface $provider = null)
    {
        $this->provider = $provider;
    }

    /**
     * Generate an SEO article using provider (calls chat)
     * @param string $topic
     * @return mixed
     */
    public function generateArticle(string $topic): array
    {
        if ($this->provider) {
            $prompt = "Write a search-optimized article about: $topic";
            return $this->provider->chat([
                ['role' => 'user', 'content' => $prompt]
            ]);
        }
        // Mock: retorna artigo fake
        return [
            'title' => 'SEO Article about ' . $topic,
            'description' => 'Description for ' . $topic,
            'content' => 'Content for ' . $topic
        ];
    }

    /**
     * Generate SEO title and description using provider (calls chat)
     * @param string $topic
     * @return mixed
     */
    public function generateMeta(string $topic): array
    {
        if ($this->provider) {
            $prompt = "Generate an SEO title and meta description for: $topic";
            return $this->provider->chat([
                ['role' => 'user', 'content' => $prompt]
            ]);
        }
        // Mock: retorna meta tags fake
        return [
            'title' => 'Meta Title for ' . $topic,
            'description' => 'Meta Description for ' . $topic
        ];
    }
}
