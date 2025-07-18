<?php

namespace Vkoori\JwtAuth\Exceptions;

use Exception;

abstract class BaseGuardException extends Exception {
    const INVALID_ACCESS_TOKEN = 1;
    const INVALID_REFRESH_TOKEN = 2;
    const REVOKED_ACCESS_TOKEN = 3;
    const INVALID_IDENTIFIER = 4;
    const NOT_SUPPORT = 5;
}
