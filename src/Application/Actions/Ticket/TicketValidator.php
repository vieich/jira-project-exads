<?php

namespace App\Application\Actions\Ticket;

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

    public function isNameValid($ticketName)
    {
        return preg_match("/^[A-Za-z]{2,8}$/", $ticketName);
    }
}