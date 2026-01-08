<?php
namespace NeuraPHP\Providers;

use NeuraPHP\Core\ProviderInterface;

class OpenAI implements ProviderInterface
{
    protected string $apiKey;
    protected array $models;
    protected string $baseUrl;
    protected mixed $lastRawResponse = null;

    public function __construct(string $apiKey, array $models = [])
    {
        $this->apiKey = $apiKey;
        $this->models = $models;
        $this->baseUrl = 'https://api.openai.com/v1/';
    }

    public function chat(array $messages): mixed
    {
        $model = $this->models['chat'] ?? 'gpt-3.5-turbo';
        $openaiMessages = [];
        foreach ($messages as $msg) {
            $openaiMessages[] = [
                'role' => $msg['role'] ?? 'user',
                'content' => $msg['content'] ?? $msg['message'] ?? ''
            ];
        }
        $data = [
            'model' => $model,
            'messages' => $openaiMessages,
        ];
        return $this->request('chat/completions', $data);
    }

    public function image(string $prompt): mixed
    {
        $model = $this->models['image'] ?? 'dall-e-3';
        $data = [
            'model' => $model,
            'prompt' => $prompt,
        ];
        $result = $this->request('images/generations', $data);
        return $this->extractUrlFromResult($result);
    }

    public function voice(string $text): mixed
    {
        $model = $this->models['voice'] ?? 'tts-1';
        $data = [
            'model' => $model,
            'input' => $text,
        ];
        $result = $this->request('audio/speech', $data);
        return $this->extractUrlFromResult($result);
    }

    public function video(string $prompt): mixed
    {
        return '';
    }

    public function embeddings(string $text): mixed
    {
        $model = $this->models['embeddings'] ?? 'text-embedding-ada-002';
        $data = [
            'model' => $model,
            'input' => $text,
        ];
        return $this->request('embeddings', $data);
    }

    public function moderation(string $text): mixed
    {
        $model = $this->models['moderation'] ?? 'text-moderation-latest';
        $data = [
            'model' => $model,
            'input' => $text,
        ];
        return $this->request('moderations', $data);
    }

    private function extractUrlFromResult(mixed $result): string
    {
        if (is_string($result)) {
            return $result;
        }
        if (is_array($result) && isset($result['url'])) {
            return $result['url'];
        }
        return '';
    }

    protected function request(string $endpoint, array $data): mixed
    {
        $ch = curl_init($this->baseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err) {
            throw new \RuntimeException('OpenAI request error: ' . $err);
        }
        $decoded = json_decode($response, true);
        $this->lastRawResponse = $decoded;
        return $decoded;
    }

    public function getLastRawResponse(): mixed
    {
        return $this->lastRawResponse;
    }
}
