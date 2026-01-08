<?php
namespace NeuraPHP\Providers;

use NeuraPHP\Core\ProviderInterface;

class Gemini implements ProviderInterface
{
    protected string $apiKey;
    protected array $models;
    protected string $baseUrl;

    public function __construct(string $apiKey, array $models = [])
    {
        $this->apiKey = $apiKey;
        $this->models = $models;
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta/';
    }

    public function chat(array $messages): mixed
    {
        // Gemini chat API (mock, real endpoint may differ)
        $model = $this->models['chat'] ?? 'gemini-pro';
        $data = [
            'model' => $model,
            'contents' => $messages,
        ];
        return $this->request('models/' . $model . ':generateContent', $data);
    }

    public function image(string $prompt): mixed
    {
        $model = $this->models['image'] ?? 'gemini-image';
        $data = [
            'model' => $model,
            'prompt' => $prompt,
        ];
        return $this->request('models/' . $model . ':generateImage', $data);
    }

    public function voice(string $text): mixed
    {
        // Not supported by Gemini (return null or throw)
        return null;
    }

    public function video(string $prompt): mixed
    {
        // Not supported by Gemini (return null or throw)
        return null;
    }

    public function embeddings(string $text): mixed
    {
        $model = $this->models['embeddings'] ?? 'gemini-embed';
        $data = [
            'model' => $model,
            'input' => $text,
        ];
        return $this->request('models/' . $model . ':embedContent', $data);
    }

    public function moderation(string $text): mixed
    {
        // Not supported by Gemini (return null or throw)
        return null;
    }

    protected function request(string $endpoint, array $data): mixed
    {
        $url = $this->baseUrl . $endpoint . '?key=' . urlencode($this->apiKey);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err) {
            throw new \Exception('Gemini request error: ' . $err);
        }
        return json_decode($response, true);
    }
}
