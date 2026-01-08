<?php
// /config/models.php
return [
    'openai' => [
        'chat' => 'gpt-3.5-turbo',
        'image' => 'dall-e-3',
        'voice' => 'tts-1',
        'video' => null,
        'embeddings' => 'text-embedding-ada-002',
        'moderation' => 'text-moderation-latest',
    ],
    'gemini' => [
        'chat' => 'gemini-pro',
        'image' => 'gemini-image',
        'voice' => null,
        'video' => null,
        'embeddings' => 'gemini-embed',
        'moderation' => null,
    ],
    'claude' => [
        'chat' => 'claude-2',
        'image' => null,
        'voice' => null,
        'video' => null,
        'embeddings' => null,
        'moderation' => null,
    ],
];
