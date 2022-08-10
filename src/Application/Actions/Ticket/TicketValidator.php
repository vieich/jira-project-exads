<?php

namespace App\Application\Actions\Ticket;

use App\Domain\Ticket\Exception\TicketNameFormatException;
use App\Domain\Ticket\Exception\TicketNoAuthorizationException;
use App\Domain\Ticket\Exception\TicketPayloadStructureException;

class TicketValidator
{
    public function checkIfTicketNameIsValid($ticketName): void
    {
        if (!preg_match("/^[A-Za-z]{2,12}$/", $ticketName)) {
            throw new TicketNameFormatException();
        }
    }

    public function isDoneValid($ticketIsDone): bool
    {
        return is_bool($ticketIsDone);
    }

    public function checkIfPayloadFormatIsValid(array $args): void
    {
        foreach ($args as $key => $value) {
            if (!isset($value) || $value == "") {
                throw new TicketPayloadStructureException('Payload is not valid, is missing the ' . $key . ' field');
            }
        }
    }

    public function checkIfHeaderIsMissing(string $header): void
    {
        if ($header == "") {
            throw new TicketNoAuthorizationException('Auth-Token is missing on the header.');
        }
    }
}
