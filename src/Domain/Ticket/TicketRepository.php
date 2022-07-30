<?php

namespace App\Domain\Ticket;

interface TicketRepository
{
    public function findAll(): array;
    public function findTicketById(): Ticket;
    public function createTicket(string $ticketName, int $creatorId, string $creatorToken);

}