<?php

namespace Vkoori\JwtAuth\Middlewares;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Vkoori\JwtAuth\Auth\Traits\JwtParserTrait;
use Vkoori\JwtAuth\Exceptions\ScopeException;
use Vkoori\LaravelJwt\Services\Jwt;

class JwtScopeMiddleware
{
    use JwtParserTrait;

    public function handle(Request $request, \Closure $next, ...$scopes)
    {
        if (Auth::guest()) {
            throw new AuthenticationException();
        }

        if (empty($scopes)) {
            return $next($request);
        }

        $token = $this->getAccess(request: $request);
        $requestScopes = Jwt::verify(token: $token)['scope'] ?? [];

        if (!array_intersect($scopes, $requestScopes)) {
            throw new ScopeException();
        }

        return $next($request);
    }
}
