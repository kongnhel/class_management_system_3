<?php

return [
    'api_key' => env('DIFY_API_KEY'),
    'api_url' => env('DIFY_API_URL', 'https://api.dify.ai/v1/chat-messages'),
    'timeout' => env('DIFY_TIMEOUT', 45),
    'rate_limit' => [
        'max_attempts' => env('DIFY_RATE_LIMIT', 10),
        'decay_minutes' => 1,
    ],
];
