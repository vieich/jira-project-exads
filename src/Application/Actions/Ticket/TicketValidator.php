<?php

namespace App\Application\Actions\Ticket;

use App\Application\Actions\Validator;
use App\Domain\Ticket\Exception\TicketNameFormatException;
use App\Domain\Ticket\Exception\TicketPayloadDataException;

class TicketValidator extends Validator
{
    public function checkIfTicketNameIsValid($ticketName): void
    {
        if (!preg_match("/^[A-Za-z]{2,12}$/", $ticketName)) {
            throw new TicketNameFormatException();
        }
    }
}
