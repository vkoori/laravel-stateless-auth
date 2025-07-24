<?php

namespace Vkoori\JwtAuth\Auth\Traits;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Vkoori\LaravelJwt\Services\Jwt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Vkoori\JwtAuth\Exceptions\InvalidIdentifierException;
use Vkoori\JwtAuth\Exceptions\InvalidRefreshTokenException;

trait HasApiTokens
{
    use JwtCacheTrait;

    public function revokeToken(string $token)
    {
        $tokenData = Jwt::verify($token);

        if (is_null($this->getAuthIdentifier())) {
            $identity = $this->findOrFail($tokenData['sub']);
            $this->attributes = $identity->getAttributes();
            $this->original = $identity->getOriginal();
        }

        if ($this->getAuthIdentifier() != $tokenData['sub']) {
            throw new InvalidIdentifierException();
        }

        if (!config('jwt-guard.jwt.enable_revoke')) {
            return false;
        }

        $this->revokeOne($tokenData['sub'], $tokenData['jti']);
        return true;
    }

    public function revokeAllTokens()
    {
        if (
            !config('jwt-guard.jwt.enable_revoke')
            || is_null($this->getAuthIdentifier())
        ) {
            return false;
        }

        $this->revokeAll($this->getAuthIdentifier());
        return true;
    }

    public function accessToken(
        string $issuer = 'android',
        array $scopes = ['customer']
    ) {
        $config = config('jwt-guard.jwt');
        $jti = (string) Str::uuid();

        $accessToken = $this->generateJwt('access', $issuer, $scopes, $config['access_token_ttl'], $jti);
        $refreshToken = $this->generateJwt('refresh', $issuer, $scopes, $config['refresh_token_ttl'], $jti);

        if ($config['enable_revoke']) {
            $this->storeAccessTokensInCache($accessToken, $this);
            $this->storeRefreshTokensInCache($refreshToken);
        }

        Auth::setUser($this);

        if ($config['set_cookie']) {
            $this->attachTokensAsCookies($accessToken, $refreshToken, $config);
            return [];
        }

        return $this->jwtResult($accessToken, $refreshToken, $config);
    }

    public function refreshToken(string $refreshToken)
    {
        $refreshTokenData = Jwt::verify($refreshToken);

        if ($refreshTokenData['aud'] != 'refresh') {
            throw new InvalidRefreshTokenException();
        }

        if (is_null($this->getAuthIdentifier())) {
            $identity = $this->findOrFail($refreshTokenData['sub']);
            $this->attributes = $identity->getAttributes();
            $this->original = $identity->getOriginal();
        }

        if ($this->getAuthIdentifier() != $refreshTokenData['sub']) {
            throw new InvalidIdentifierException();
        }

        if (is_null($this->retrieveRefreshTokensInCache($refreshTokenData['sub'], $refreshTokenData['jti']))) {
            throw new InvalidRefreshTokenException();
        }

        $result = $this->accessToken(
            issuer: $refreshTokenData['iss'],
            scopes: $refreshTokenData['scope']
        );

        if (config('jwt-guard.jwt.enable_revoke')) {
            $this->revokeOne($refreshTokenData['sub'], $refreshTokenData['jti']);
        }

        return $result;
    }

    protected function generateJwt(
        string $audience,
        string $issuer,
        array $scopes,
        int $ttl,
        string $jti
    ) {
        return Jwt::sign()
            ->setAudience($audience)
            ->setIssuer($issuer)
            ->setSubject($this->getAuthIdentifier())
            ->setScopes($scopes)
            ->setJwtId($jti)
            ->setExpirationTime(Carbon::now()->addSeconds($ttl))
            ->encode();
    }

    protected function attachTokensAsCookies(
        string $accessToken,
        string $refreshToken,
        array $config
    ) {
        $domain = Str::wildcard(request()->getSchemeAndHttpHost());

        $accessCookie = $this->makeCookie(
            $config['parser']['access']['cookie'],
            $accessToken,
            $config['access_token_ttl'],
            $domain
        );
        Cookie::queue($accessCookie);

        $refreshCookie = $this->makeCookie(
            $config['parser']['refresh']['cookie'],
            $refreshToken,
            $config['refresh_token_ttl'],
            $domain
        );
        Cookie::queue($refreshCookie);
    }

    protected function makeCookie(string $name, string $value, int $ttl, string $domain)
    {
        return Cookie::make(
            $name,
            $value,
            $ttl / 60,
            '/',
            $domain,
            false,
            true,
            false,
            'Lax'
        );
    }

    protected function jwtResult(string $accessToken, string $refreshToken, array $config)
    {
        return [
            'jwt' => [
                'access_token' => [
                    'token' => $accessToken,
                    'ttl' => $config['access_token_ttl']
                ],
                'refresh_token' => [
                    'token' => $refreshToken,
                    'ttl' => $config['refresh_token_ttl']
                ]
            ]
        ];
    }
}
