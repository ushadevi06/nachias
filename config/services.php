<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'orderaxe' => [
        'base_url' => env('ORDERAXE_BASE_URL'),
        'client_id' => env('ORDERAXE_CLIENT_ID'),
        'client_secret' => env('ORDERAXE_CLIENT_SECRET'),
    ],

    'ollama' => [
        'url' => env('OLLAMA_URL', 'http://127.0.0.1:11434'),
        'model' => env('OLLAMA_MODEL', 'qwen2.5-coder:1.5b'),
        'timeout' => (int) env('OLLAMA_TIMEOUT', 120),
        'num_ctx' => (int) env('OLLAMA_NUM_CTX', 8192),
        'system_prompt_file' => env('OLLAMA_SYSTEM_PROMPT_FILE', 'system_prompt.txt'),
        'system_prompt' => env('OLLAMA_SYSTEM_PROMPT', ''),
    ],

];
