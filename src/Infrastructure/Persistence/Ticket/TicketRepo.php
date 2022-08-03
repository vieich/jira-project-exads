<?php

namespace App\Infrastructure\Persistence\Ticket;

use App\Domain\Ticket\Exception\TicketNotFoundException;
use App\Domain\Ticket\Ticket;
use App\Domain\Ticket\TicketRepository;
use App\Infrastructure\Persistence\Database;
use PDO;

class TicketRepo extends Database implements TicketRepository
{

    public function findAll(): array
    {
        return [];
    }

    public function findTicketById(): Ticket
    {
        return new Ticket(1, 'test', 1, false);
    }

    public function createTicket(string $ticketName, int $creatorId): Ticket
    {
        $query = "INSERT INTO tickets (name, user_id) VALUE (:name, :user_id)";

        $stmt = $this->connection->prepare($query);
        $stmt->bindValue('name', $ticketName);
        $stmt->bindValue('user_id', $creatorId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            throw new TicketNotFoundException('Error creating ticket');
        }

        return new Ticket(
            (int) $this->connection->lastInsertId(),
            $ticketName,
            $creatorId,
            false
        );
    }

    public function deleteTicket(int $ticketId)
    {
        $query = 'DELETE FROM tickets WHERE id = :id';

        $stmt = $this->connection->prepare($query);
        $stmt->bindValue('id', $ticketId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            throw new TicketNotFoundException();
        }

        return [
            'message' => 'Ticket with id ' . $ticketId . ' deleted',
            'hasSuccess' => true
        ];
    }

    public function updateTicket(int $ticketId, array $valuesToUpdate)
    {
        $name = array_key_exists('name', $valuesToUpdate) ? $valuesToUpdate['name'] : null;
        $isDone = array_key_exists('isDone', $valuesToUpdate) ? $valuesToUpdate['isDone'] : null;
    }
}
