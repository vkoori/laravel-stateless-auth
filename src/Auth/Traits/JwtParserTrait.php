<?php

namespace Vkoori\JwtAuth\Auth\Traits;

use Illuminate\Http\Request;

trait JwtParserTrait
{
    public function getAccess(Request $request): ?string
    {
        $config = config('jwt-guard.jwt.parser.access');

        $accessToken = $request->cookies->get($config['cookie'])
            ?? $request->headers->get($config['header'])
            ?? $request->query->get($config['query_string']);

        if (strpos($accessToken, 'Bearer ') === 0) {
            $accessToken = substr($accessToken, 7);
        }

        return $accessToken;
    }

    public function getRefresh(Request $request): ?string
    {
        $config = config('jwt-guard.jwt.parser.refresh');

        return $request->cookies->get($config['cookie'])
            ?? $request->headers->get($config['header'])
            ?? $request->get($config['body']);
    }
}
