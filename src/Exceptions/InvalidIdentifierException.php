<?php

namespace Vkoori\JwtAuth\Exceptions;

class InvalidIdentifierException extends BaseGuardException
{
    public function __construct()
    {
        parent::__construct(
            self::INVALID_IDENTIFIER,
            "The identifier token is invalid!"
        );
    }
}
