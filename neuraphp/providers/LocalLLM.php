/**
 * Exception thrown when a prompt is missing for LLM inference.
 */
class LLMInvalidPromptException extends \Exception {}
<?php
/**
 * NeuraPHP AI Engine™
 * Local LLM Provider (Ollama Integration, Stage 7)
 *
 * @package NeuraPHP\Providers
 * @author  NeuraPHP Team
 * @license MIT
 */

namespace NeuraPHP\Providers;

use Exception;

/**
 * Class LocalLLM
 * Integrates with Ollama for local/offline inference (mock)
 */
class LocalLLM
{
    /**
     * Run inference on a prompt using Ollama (mock)
     * @param string $prompt
     * @return string
     * @throws Exception
     */
    public function infer(string $prompt): string
    {
        // In production, connect to Ollama server or local LLM API
        // For demo, return a mock response
        if (empty($prompt)) {
            throw new LLMInvalidPromptException('Prompt required');
        }
        return "[Ollama LLM] Response to: $prompt";
    }
}
