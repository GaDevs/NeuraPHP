<?php
namespace NeuraPHP\Core;

interface ProviderInterface
{
    /**
     * Chat completion (LLM)
     * @param array $messages
     * @return mixed
     */
    public function chat(array $messages): mixed;

    /**
     * Image generation
     * @param string $prompt
     * @return mixed
     */
    public function image(string $prompt): mixed;

    /**
     * Voice generation
     * @param string $text
     * @return mixed
     */
    public function voice(string $text): mixed;

    /**
     * Video generation
     * @param string $prompt
     * @return mixed
     */
    public function video(string $prompt): mixed;

    /**
     * Embeddings
     * @param string $text
     * @return mixed
     */
    public function embeddings(string $text): mixed;

    /**
     * Moderation
     * @param string $text
     * @return mixed
     */
    public function moderation(string $text): mixed;

    /**
     * Get last raw response from API
     * @return mixed
     */
    public function getLastRawResponse(): mixed;
}
