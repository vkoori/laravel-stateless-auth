<?php

namespace Vkoori\JwtAuth\Exceptions;

class RevokedAccessTokenException extends BaseGuardException
{
    public function __construct()
    {
        parent::__construct(
            self::REVOKED_ACCESS_TOKEN,
            'Your access token has been revoked.'
        );
    }
}
