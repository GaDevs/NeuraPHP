<?php
namespace NeuraPHP\Providers;

use NeuraPHP\Core\ProviderInterface;

class OpenAI implements ProviderInterface
{
    protected string $apiKey;
    protected array $models;
    protected string $baseUrl;

    public function __construct(string $apiKey, array $models = [])
    {
        $this->apiKey = $apiKey;
        $this->models = $models;
        $this->baseUrl = 'https://api.openai.com/v1/';
    }

    public function chat(array $messages): mixed
    {
        // Example: call OpenAI chat API
        $model = $this->models['chat'] ?? 'gpt-3.5-turbo';
        $data = [
            'model' => $model,
            'messages' => $messages,
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
        return $this->request('images/generations', $data);
    }

    public function voice(string $text): mixed
    {
        $model = $this->models['voice'] ?? 'tts-1';
        $data = [
            'model' => $model,
            'input' => $text,
        ];
        return $this->request('audio/speech', $data);
    }

    public function video(string $prompt): mixed
    {
        // Not supported by OpenAI (return null or throw)
        return null;
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
            throw new \Exception('OpenAI request error: ' . $err);
        }
        return json_decode($response, true);
    }
}
