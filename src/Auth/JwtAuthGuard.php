<?php

namespace Vkoori\JwtAuth\Auth;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use Vkoori\JwtAuth\Auth\Traits\JwtParserTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider as AuthProvider;
use Vkoori\JwtAuth\Exceptions\InvalidAccessTokenException;

class JwtAuthGuard implements Guard
{
    use JwtParserTrait;

    private ?Authenticatable $authenticatable;
    private bool $authInitialized = false;

    public function __construct(
        protected AuthProvider $authProvider,
        protected Request $request,
    ) {}

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check()
    {
        $this->setAuthenticatable(null);

        $accessToken = $this->getAccess($this->request);

        $authenticatable = null;
        if ($accessToken) {
            try {
                $authenticatable = $this->authProvider->retrieveById(identifier: $accessToken);
            } catch (InvalidAccessTokenException $th) {
                // noting
            }
        }
        $this->setAuthenticatable(authenticatable: $authenticatable);

        return !empty($this->authenticatable);
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|string|null
     */
    public function id()
    {
        return $this->getAuthenticatable()?->getAuthIdentifier();
    }

    /**
     * Get the currently authenticated user.
     *
     * @return Authenticatable|null
     */
    public function user()
    {
        return $this->getAuthenticatable();
    }

    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest()
    {
        return !$this->check();
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return $this->check();
    }

    /**
     * Determine if the guard has a user instance.
     *
     * @return bool
     */
    public function hasUser()
    {
        return $this->check();
    }

    /**
     * Set the current user.
     *
     * @param  Authenticatable  $authenticatable
     * @return void
     */
    public function setUser(Authenticatable $authenticatable)
    {
        $this->setAuthenticatable($authenticatable);
    }

    public function getProvider(): AuthProvider
    {
        return $this->authProvider;
    }

    private function setAuthenticatable(?Authenticatable $authenticatable): static
    {
        $this->authenticatable = $authenticatable;
        $this->authInitialized = true;

        return $this;
    }

    private function getAuthenticatable(): ?Authenticatable
    {
        if (!$this->authInitialized) {
            $this->check();
        }

        return $this->authenticatable;
    }
}
