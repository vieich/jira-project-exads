<?php

namespace App\Domain\Permission\Exception;

use App\Domain\DomainException\DomainRecordWithoutAuthorizationException;

class PermissionLoginException extends DomainRecordWithoutAuthorizationException
{
    public $message = 'Failed login, wrong username or password.';
}