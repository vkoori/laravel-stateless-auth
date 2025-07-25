<?php

namespace Vkoori\JwtAuth\Auth\Traits;

use Vkoori\LaravelJwt\Services\Jwt;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Cache;

trait JwtCacheTrait
{
    protected function getCacheAccessTokenKey(int $sub, string $jti)
    {
        return "jwt:access_token:{$sub}:{$jti}";
    }

    protected function getCacheRefreshTokenKey(int $sub, string $jti)
    {
        return "jwt:refresh_token:{$sub}:{$jti}";
    }

    protected function getAuthTag(int $sub)
    {
        return "jwt:auth_tags:{$sub}";
    }

    protected function retrieveAccessTokensInCache(int $sub, string $jti)
    {
        return Cache::driver($this->getCacheDriver())
            ->tags([
                $this->getAuthTag($sub)
            ])->get(
                $this->getCacheAccessTokenKey($sub, $jti)
            );
    }

    protected function retrieveRefreshTokensInCache(int $sub, string $jti)
    {
        return Cache::driver($this->getCacheDriver())
            ->tags([
                $this->getAuthTag($sub)
            ])->get(
                $this->getCacheRefreshTokenKey($sub, $jti)
            );
    }

    protected function storeAccessTokensInCache(string $accessToken, User $authenticatable)
    {
        $accessTokenData = Jwt::verify($accessToken);

        Cache::driver($this->getCacheDriver())
            ->tags([
                $this->getAuthTag($accessTokenData['sub']),
            ])->put(
                $this->getCacheAccessTokenKey($accessTokenData['sub'], $accessTokenData['jti']),
                $authenticatable,
                $accessTokenData['exp'] - time()
            );
    }

    protected function storeRefreshTokensInCache(string $refreshToken)
    {
        $refreshTokenData = Jwt::verify($refreshToken);

        Cache::driver($this->getCacheDriver())
            ->tags([
                $this->getAuthTag($refreshTokenData['sub']),
            ])->put(
                $this->getCacheRefreshTokenKey($refreshTokenData['sub'], $refreshTokenData['jti']),
                1,
                $refreshTokenData['exp'] - time()
            );
    }

    protected function revokeAll(int $sub): void
    {
        Cache::driver($this->getCacheDriver())
            ->tags([
                $this->getAuthTag($sub)
            ])->flush();
    }

    protected function revokeOne(int $sub, string $jti): void
    {
        Cache::driver($this->getCacheDriver())
            ->tags([
                $this->getAuthTag($sub)
            ])->forget(
                $this->getCacheAccessTokenKey($sub, $jti)
            );
        Cache::driver($this->getCacheDriver())
            ->tags([
                $this->getAuthTag($sub)
            ])->forget(
                $this->getCacheRefreshTokenKey($sub, $jti)
            );
    }

    private function getCacheDriver(): ?string
    {
        return property_exists($this, 'jwtCacheDriver') && $this->jwtCacheDriver
            ? $this->jwtCacheDriver
            : null;
    }
}
