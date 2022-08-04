<?php

namespace App\Domain\Ticket\Exception;

use App\Domain\DomainException\DomainCreationException;

class TicketCreateException extends DomainCreationException
{
    public $message = 'There was an error while creating the Ticket.';
}