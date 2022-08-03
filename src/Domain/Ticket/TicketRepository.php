<?php

namespace App\Domain\Ticket;

interface TicketRepository
{
    public function findAll(): array;
    public function findTicketById(): Ticket;
    public function createTicket(string $ticketName, int $creatorId);
    public function deleteTicket(int $ticketId);
    public function updateTicket(int $ticketId, array $valuesToUpdate);

}