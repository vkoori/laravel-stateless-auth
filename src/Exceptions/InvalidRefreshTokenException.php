<?php

namespace Vkoori\JwtAuth\Exceptions;

class InvalidRefreshTokenException extends BaseGuardException
{
    public function __construct()
    {
        parent::__construct(
            self::INVALID_REFRESH_TOKEN,
            "Your refresh token is not valid."
        );
    }
}
