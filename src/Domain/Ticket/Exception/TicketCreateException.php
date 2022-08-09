<?php

namespace App\Domain\Ticket\Exception;

use App\Domain\DomainException\DomainOperationException;

class TicketCreateException extends DomainOperationException
{
    public $message = 'There was an error while creating the Ticket.';
}