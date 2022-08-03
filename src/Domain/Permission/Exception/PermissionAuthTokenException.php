<?php

namespace App\Domain\Permission\Exception;

use App\Domain\DomainException\DomainPayloadDataValidatorException;

class PermissionAuthTokenException extends DomainPayloadDataValidatorException
{
    public $message = 'Authentication token must be an hexadecimal and have 64 chars.';
}