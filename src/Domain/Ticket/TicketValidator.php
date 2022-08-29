<?php

namespace App\Domain\Ticket;

use App\Domain\Ticket\Exception\TicketNameFormatException;
use App\Domain\Validator;

class TicketValidator extends Validator
{
    public function checkIfTicketNameIsValid($ticketName): void
    {
        if (!preg_match("/^[A-Za-z]{2,25}$/", $ticketName)) {
            throw new TicketNameFormatException();
        }
    }
}
