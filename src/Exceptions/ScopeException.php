<?php

namespace Vkoori\JwtAuth\Exceptions;

use Illuminate\Validation\UnauthorizedException;

class ScopeException extends UnauthorizedException
{
    public function __construct()
    {
        parent::__construct('Insufficient scope permissions.');
    }
}
