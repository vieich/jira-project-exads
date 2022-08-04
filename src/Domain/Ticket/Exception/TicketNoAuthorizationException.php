<?php

namespace App\Domain\Ticket\Exception;

use App\Domain\DomainException\DomainRecordWithoutAuthorizationException;

class TicketNoAuthorizationException extends DomainRecordWithoutAuthorizationException
{
    public $message = 'No authorization.';
}