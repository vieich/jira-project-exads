<?php

namespace App\Domain\Permission\Exception;

use App\Domain\DomainException\DomainCredentialsException;
use App\Domain\DomainException\DomainRecordWithoutAuthorizationException;

class PermissionAuthTokenException extends DomainCredentialsException
{
    public $message = 'Authentication token must be an hexadecimal and have 64 chars.';
}