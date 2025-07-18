<?php

return [
    'jwt' => [
        'parser' => [
            'access' =>
            [
                'header' => 'Authorization',
                'cookie' => 'jwt',
                'query_string' => 'jwt',
            ],
            'refresh' =>
            [
                'header' => 'Authorization',
                'cookie' => 'refresh',
                'body' => 'refresh',
            ],
        ],
        'enable_revoke' => true,
        'set_cookie' => false,
        'access_token_ttl' => 60 * 60 * 24,
        'refresh_token_ttl' => 60 * 60 * 24 * 7,
    ]
];
