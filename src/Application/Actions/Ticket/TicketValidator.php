<?php

namespace App\Application\Actions\Ticket;

use _PHPStan_9a6ded56a\Nette\Neon\Exception;
use App\Domain\Ticket\Exception\TicketPayloadDataException;
use App\Domain\Ticket\Exception\TicketPayloadStructureException;

class TicketValidator
{
    private static $instance;

    public static function getInstance(): TicketValidator
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function checkIfTicketNameIsValid($ticketName): void
    {
        if (!preg_match("/^[A-Za-z]{2,8}$/", $ticketName)) {
            throw new TicketPayloadDataException('Username field does not meed the rules, check documentation');
        }
    }

    public function isDoneValid($ticketIsDone): bool
    {
        return is_bool($ticketIsDone);
    }

    public function checkIfPayloadIsValid(array $args): void
    {
        foreach ($args as $key => $value) {
            if (!isset($value)) {
                throw new TicketPayloadStructureException('Payload is not valid, is missing the ' . $key . ' field');
            }
        }
    }
}
