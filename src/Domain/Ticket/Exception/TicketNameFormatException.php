<?php

namespace App\Domain\Ticket\Exception;

use App\Domain\DomainException\DomainDataFormatException;

class TicketNameFormatException extends DomainDataFormatException
{
    public $message = 'Ticket name does not accept special characters, and must have 3 to 13 characters';
}