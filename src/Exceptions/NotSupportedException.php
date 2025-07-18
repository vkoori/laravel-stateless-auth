<?php

namespace Vkoori\JwtAuth\Exceptions;

class NotSupportedException extends BaseGuardException
{
    public function __construct()
    {
        parent::__construct(
            self::NOT_SUPPORT,
            "This method is not supported."
        );
    }
}
