<?php
// /config/models.php
return [
    'openai' => [
        'chat' => 'gpt-3.5-turbo',
        'chat_models' => [
            'gpt-4o' => 'GPT-4o (multimodal, mais avançado)',
            'gpt-4-turbo' => 'GPT-4 Turbo',
            'gpt-4' => 'GPT-4',
            'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
        ],
        'image' => 'dall-e-3',
        'image_models' => [
            'dall-e-3' => 'DALL·E 3',
            'dall-e-2' => 'DALL·E 2',
        ],
        'voice' => 'tts-1',
        'voice_models' => [
            'tts-1' => 'TTS 1',
            'tts-1-hd' => 'TTS 1 HD',
        ],
        'video' => null,
        'embeddings' => 'text-embedding-ada-002',
        'moderation' => 'text-moderation-latest',
    ],
    'gemini' => [
        'chat' => 'gemini-3-pro',
        'chat_models' => [
            'gemini-3-pro' => 'Gemini 3 Pro (mais avançado, estável)',
            'gemini-2.0-flash' => 'Gemini 2.0 Flash (rápido)',
            'gemini-2.0-flash-lite' => 'Gemini 2.0 Flash Lite (ultra rápido)',
            'gemini-1.5-pro' => 'Gemini 1.5 Pro',
            'gemini-1.5-flash' => 'Gemini 1.5 Flash',
        ],
        'image' => 'gemini-3-pro',
        'voice' => null,
        'video' => null,
        'embeddings' => 'text-embedding-004',
        'moderation' => null,
    ],
    'claude' => [
        'chat' => 'claude-3-opus-20240229',
        'chat_models' => [
            'claude-3-opus-20240229' => 'Claude 3 Opus',
            'claude-3-sonnet-20240229' => 'Claude 3 Sonnet',
            'claude-3-haiku-20240307' => 'Claude 3 Haiku',
            'claude-2.1' => 'Claude 2.1',
            'claude-2.0' => 'Claude 2.0',
        ],
        'image' => null,
        'voice' => null,
        'video' => null,
        'embeddings' => null,
        'moderation' => null,
    ],
];
