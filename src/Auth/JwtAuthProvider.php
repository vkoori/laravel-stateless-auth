<?php

namespace Vkoori\JwtAuth\Auth;

use Vkoori\JwtAuth\Auth\Traits\JwtCacheTrait;
use Vkoori\JwtAuth\Exceptions\InvalidAccessTokenException;
use Vkoori\JwtAuth\Exceptions\NotSupportedException;
use Vkoori\JwtAuth\Exceptions\RevokedAccessTokenException;
use Vkoori\LaravelJwt\Services\Jwt;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class JwtAuthProvider implements UserProvider
{
    use JwtCacheTrait;

    public function __construct(protected string $model) {}

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        $jwt = Jwt::verify(token: $identifier);

        if ($jwt['aud'] != 'access') {
            throw new InvalidAccessTokenException();
        }

        if (config('jwt-guard.jwt.enable_revoke')) {
            $token = $this->retrieveAccessTokensInCache(sub: $jwt['sub'], jti: $jwt['jti']);
            if (is_null($token)) {
                throw new RevokedAccessTokenException();
            }
        }

        return $this->retrieveByQuery(userId: $jwt['sub']);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string  $token
     * @return Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        throw new NotSupportedException();
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  Authenticatable  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        throw new NotSupportedException();
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        throw new NotSupportedException();
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        throw new NotSupportedException();
    }

    public function rehashPasswordIfRequired(
        Authenticatable $user,
        #[\SensitiveParameter] array $credentials,
        bool $force = false
    ) {
        throw new NotSupportedException();
    }

    protected function createModel(): Model
    {
        $class = '\\' . ltrim($this->model, '\\');

        return new $class();
    }

    protected function retrieveByQuery(string|int $userId): Authenticatable|null
    {
        return $this->createModel()->newQuery()->find($userId);
    }
}
