<?php
namespace NeuraPHP\Providers;

use NeuraPHP\Core\ProviderInterface;

class Gemini implements ProviderInterface
{
    protected string $apiKey;
    protected array $models;
    protected string $baseUrl;
    protected mixed $lastRawResponse = null;
    private const MODEL_ENDPOINT_PREFIX = 'models/';

    public function __construct(string $apiKey, array $models = [])
    {
        $this->apiKey = $apiKey;
        $this->models = $models;
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1/';
    }

    public function chat(array $messages): mixed
    {
        $model = $this->models['chat'] ?? 'gemini-1.5-pro-latest';
        $endpoint = self::MODEL_ENDPOINT_PREFIX . $model . ':generateContent';
        // Gemini espera contents como array de objetos {role, parts}
        $contents = [];
        foreach ($messages as $msg) {
            $role = $msg['role'] ?? 'user';
            $text = $msg['content'] ?? $msg['message'] ?? '';
            $contents[] = [
                'role' => $role,
                'parts' => [ [ 'text' => $text ] ]
            ];
        }
        $data = [
            'contents' => $contents
        ];
        $result = $this->request($endpoint, $data);
        // Para compatibilidade, retorna resposta Gemini já no formato esperado pelo módulo Chat
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            return [
                'choices' => [
                    [ 'message' => [ 'content' => $result['candidates'][0]['content']['parts'][0]['text'] ] ]
                ]
            ];
        }
        return $result;
    }

    public function image(string $prompt): mixed
    {
        $model = $this->models['image'] ?? 'gemini-1.5-pro-latest';
        $data = [
            'model' => $model,
            'prompt' => $prompt,
        ];
        $result = $this->request(self::MODEL_ENDPOINT_PREFIX . $model . ':generateImage', $data);
        return $this->extractImageUrl($result);
    }

    private function extractImageUrl(mixed $result): string
    {
        if (is_string($result)) {
            return $result;
        }
        if (is_array($result) && isset($result['url'])) {
            return $result['url'];
        }
        return '';
    }

    public function voice(string $text): mixed
    {
        // Not supported by Gemini (return empty string)
        return '';
    }

    public function video(string $prompt): mixed
    {
        // Not supported by Gemini (return empty string)
        return '';
    }

    public function embeddings(string $text): mixed
    {
        $model = $this->models['embeddings'] ?? 'gemini-embed';
        $data = [
            'model' => $model,
            'input' => $text,
        ];
        return $this->request(self::MODEL_ENDPOINT_PREFIX . $model . ':embedContent', $data);
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
            throw new \RuntimeException('Gemini request error: ' . $err);
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
