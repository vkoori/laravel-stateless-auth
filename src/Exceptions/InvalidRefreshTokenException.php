<?php

namespace Vkoori\JwtAuth\Exceptions;

class InvalidRefreshTokenException extends BaseGuardException
{
    public function __construct()
    {
        parent::__construct(
            "Your refresh token is not valid.",
            self::INVALID_REFRESH_TOKEN
        );
    }
}
