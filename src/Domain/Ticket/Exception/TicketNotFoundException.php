<?php

namespace App\Domain\Ticket\Exception;

use App\Domain\DomainException\DomainRecordNotFoundException;

class TicketNotFoundException extends DomainRecordNotFoundException
{
    public $message = 'The ticket does not exist.';
}