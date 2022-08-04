<?php

namespace App\Domain\Tab\Exception;

use App\Domain\DomainException\DomainRecordWithoutAuthorizationException;

class TabNoAuthorizationException extends DomainRecordWithoutAuthorizationException
{
    public $message = 'No authorization.';
}