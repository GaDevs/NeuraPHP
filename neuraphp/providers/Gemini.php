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
            $model = $this->models['chat'] ?? 'gemini-1.5-pro-latest';
            $endpoint = 'models/' . $model . ':generateContent';
            // Converter mensagens para formato Gemini
            $contents = [];
            foreach ($messages as $msg) {
                $contents[] = [
                    'role' => $msg['role'] ?? 'user',
                    'parts' => [ [ 'text' => $msg['content'] ?? $msg['message'] ?? '' ] ]
                ];
            }
            $data = [ 'contents' => $contents ];
            return $this->request($endpoint, $data);
    }

    public function image(string $prompt): mixed
    {
        $model = $this->models['image'] ?? 'gemini-1.5-pro-latest';
        $data = [
            'model' => $model,
            'prompt' => $prompt,
        ];
        $result = $this->request('models/' . $model . ':generateImage', $data);
        return is_string($result) ? $result : (is_array($result) && isset($result['url']) ? $result['url'] : '');
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
            $url = $this->baseUrl . $endpoint;
            $ch = curl_init($url);
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
                throw new \Exception('Gemini request error: ' . $err);
            }
            return json_decode($response, true);
    }
}
