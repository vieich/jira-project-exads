<?php

namespace App\Domain\User\Exception;

use App\Domain\DomainException\DomainCredentialsException;
use App\Domain\DomainException\DomainRecordWithoutAuthorizationException;

class UserNoAuthorizationException extends DomainCredentialsException
{
    public $message = 'The user as no authorization.';
}