<?php

namespace Vkoori\JwtAuth\Exceptions;

class NotSupportedException extends BaseGuardException
{
    public function __construct()
    {
        parent::__construct(
            "This method is not supported.",
            self::NOT_SUPPORT
        );
    }
}
