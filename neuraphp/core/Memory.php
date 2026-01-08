<?php
/**
 * NeuraPHP AI Engine™
 * Conversation Memory (Stage 2)
 *
 * @package NeuraPHP\Core
 * @author  NeuraPHP Team
 * @license MIT
 */

namespace NeuraPHP\Core;

/**
 * Class Memory
 * Handles persistent storage of chat history
 */
class Memory
{
    /**
     * @var string Memory storage directory
     */
    protected string $memoryDir;

    /**
     * Memory constructor
     * @param string|null $memoryDir
     */
    public function __construct(?string $memoryDir = null)
    {
        $this->memoryDir = $memoryDir ?? __DIR__ . '/../../memory';
        if (!is_dir($this->memoryDir)) {
            mkdir($this->memoryDir, 0775, true);
        }
    }

    /**
     * Get conversation history
     * @param string $conversationId
     * @return array
     */
    public function getHistory(string $conversationId): array
    {
        $file = $this->memoryDir . '/' . md5($conversationId) . '.json';
        if (!file_exists($file)) {
            return [];
        }
        $data = file_get_contents($file);
        return json_decode($data, true) ?: [];
    }

    /**
     * Save conversation history
     * @param string $conversationId
     * @param array $history
     * @return void
     */
    public function saveHistory(string $conversationId, array $history): void
    {
        $file = $this->memoryDir . '/' . md5($conversationId) . '.json';
        file_put_contents($file, json_encode($history, JSON_PRETTY_PRINT));
    }
}
