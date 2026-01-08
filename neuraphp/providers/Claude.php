<?php
namespace NeuraPHP\Providers;

use NeuraPHP\Core\ProviderInterface;

class Claude implements ProviderInterface
{
    protected string $apiKey;
    protected array $models;
    protected string $baseUrl;

    public function __construct(string $apiKey, array $models = [])
    {
        $this->apiKey = $apiKey;
        $this->models = $models;
        $this->baseUrl = 'https://api.anthropic.com/v1/';
    }

    public function chat(array $messages): mixed
    {
        $model = $this->models['chat'] ?? 'claude-2';
            $model = $this->models['chat'] ?? 'claude-3-opus-20240229';
            // Converter mensagens para formato Anthropic
            $claudeMessages = [];
            foreach ($messages as $msg) {
                $claudeMessages[] = [
                    'role' => $msg['role'] ?? 'user',
                    'content' => $msg['content'] ?? $msg['message'] ?? ''
                ];
            }
            $data = [
                'model' => $model,
                'max_tokens' => 1024,
                'messages' => $claudeMessages,
            ];
            return $this->request('messages', $data);
    }

    public function image(string $prompt): mixed
    {
        // Not supported by Claude (return empty string)
        return '';
    }

    public function voice(string $text): mixed
    {
        // Not supported by Claude (return empty string)
        return '';
    }

    public function video(string $prompt): mixed
    {
        // Not supported by Claude (return empty string)
        return '';
    }

    public function embeddings(string $text): mixed
    {
        // Not supported by Claude (return null or throw)
        return null;
    }

    public function moderation(string $text): mixed
    {
        // Not supported by Claude (return null or throw)
        return null;
    }

    protected function request(string $endpoint, array $data): mixed
    {
        $ch = curl_init($this->baseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'x-api-key: ' . $this->apiKey,
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err) {
            throw new \Exception('Claude request error: ' . $err);
        }
        return json_decode($response, true);
    }
}
