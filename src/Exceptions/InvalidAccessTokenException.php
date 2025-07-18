<?php

namespace Vkoori\JwtAuth\Exceptions;

class InvalidAccessTokenException extends BaseGuardException
{
    public function __construct()
    {
        parent::__construct(
            self::INVALID_ACCESS_TOKEN,
            "Your access token is not valid."
        );
    }
}
