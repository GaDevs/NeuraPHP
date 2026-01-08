<?php
/**
 * NeuraPHP AI Engine™
 * Chat Module (Stage 2)
 *
 * @package NeuraPHP\Modules
 * @author  NeuraPHP Team
 * @license MIT
 */


namespace NeuraPHP\Modules;

use NeuraPHP\Core\Memory;
use NeuraPHP\Core\ProviderInterface;

/**
 * Class Chat
 * Handles persistent chat and conversation logic
 */
class Chat
{
    /**
     * @var Memory
     */
    protected Memory $memory;

    /**
     * @var ProviderInterface|null
     */
    protected ?ProviderInterface $provider = null;

    /**
     * Chat constructor
     * @param Memory $memory
     * @param ProviderInterface|null $provider
     */
    public function __construct(Memory $memory, ?ProviderInterface $provider = null)
    {
        $this->memory = $memory;
        $this->provider = $provider;
    }

    /**
     * Add a message to a conversation
     * @param string $conversationId
     * @param string $role
     * @param string $message
     * @return void
     */
    public function addMessage(string $conversationId, string $role, string $message): void
    {
        $history = $this->memory->getHistory($conversationId);
        $history[] = [
            'role' => $role,
            'message' => $message,
            'timestamp' => time()
        ];
        $this->memory->saveHistory($conversationId, $history);
    }

    /**
     * Get AI response from provider and add to conversation
     * @param string $conversationId
     * @return string|null
     */
    public function getAIResponse(string $conversationId): ?string
    {
        if (!$this->provider) { return null; }
        $history = $this->memory->getHistory($conversationId);
        // Convert to provider message format if needed
        $messages = array_map(function($msg) {
            return [
                'role' => $msg['role'],
                'content' => $msg['message']
            ];
        }, $history);
        $response = $this->provider->chat($messages);
        // Assume response is array with 'choices' and 'message' (OpenAI style)
        if (isset($response['choices'][0]['message']['content'])) {
            $aiMsg = $response['choices'][0]['message']['content'];
            $this->addMessage($conversationId, 'assistant', $aiMsg);
            return $aiMsg;
        }
        return null;
    }

    /**
     * Get conversation history
     * @param string $conversationId
     * @return array
     */
    public function getHistory(string $conversationId): array
    {
        return $this->memory->getHistory($conversationId);
    }
}
