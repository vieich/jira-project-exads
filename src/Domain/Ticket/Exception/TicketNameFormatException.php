<?php

namespace App\Domain\Ticket\Exception;

use App\Domain\DomainException\DomainDataFormatException;

class TicketNameFormatException extends DomainDataFormatException
{
    public $message = 'Ticket name invalid, only letters, numbers and _ accepted, and must have 3 to 13 characters';
}