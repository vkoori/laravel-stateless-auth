<?php

namespace Vkoori\JwtAuth\Exceptions;

class InvalidIdentifierException extends BaseGuardException
{
    public function __construct()
    {
        parent::__construct(
            "The identifier token is invalid!",
            self::INVALID_IDENTIFIER
        );
    }
}
