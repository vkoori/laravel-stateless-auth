<?php

namespace Vkoori\JwtAuth\Exceptions;

class InvalidAccessTokenException extends BaseGuardException
{
    public function __construct()
    {
        parent::__construct(
            "Your access token is not valid.",
            self::INVALID_ACCESS_TOKEN
        );
    }
}
