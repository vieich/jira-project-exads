<?php

namespace App\Domain\Ticket;

use App\Domain\Ticket\Exception\TicketNameFormatException;
use App\Domain\Validator;

class TicketValidator extends Validator
{
    /**
     * @throws TicketNameFormatException
     */
    public function checkIfTicketNameIsValid($ticketName): void
    {
        if (!preg_match("/^\\w{3,13}$/", $ticketName)) {
            throw new TicketNameFormatException();
        }
    }
}
