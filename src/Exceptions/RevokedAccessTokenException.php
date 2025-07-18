<?php

namespace Vkoori\JwtAuth\Exceptions;

class RevokedAccessTokenException extends BaseGuardException
{
    public function __construct()
    {
        parent::__construct(
            'Your access token has been revoked.',
            self::REVOKED_ACCESS_TOKEN
        );
    }
}
