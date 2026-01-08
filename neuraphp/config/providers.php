<?php
// /config/providers.php
return [
    'openai' => [
        'name' => 'OpenAI',
        'base_url' => 'https://api.openai.com/v1/',
    ],
    'gemini' => [
        'name' => 'Gemini',
        'base_url' => 'https://generativelanguage.googleapis.com/v1beta/',
    ],
    'claude' => [
        'name' => 'Claude',
        'base_url' => 'https://api.anthropic.com/v1/',
    ],
];
