<?php

namespace App\Domain\User;

use App\Domain\DomainException\DomainRecordWithoutAuthorizationException;

class UserNoAuthorizationException extends DomainRecordWithoutAuthorizationException
{
    public $message = 'The user as no authorization.';
}