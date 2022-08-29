<?php

namespace App\Domain\Ticket;

interface TicketRepository
{
    public function findAll(bool $showHistory): array;
    public function findTicketById(int $ticketId): Ticket;
    public function createTicket(string $ticketName, int $creatorId): Ticket;
    public function deleteTicket(int $ticketId);
    public function updateTicket(int $ticketId, string $ticketName): Ticket;

}