<?php

namespace App\Infrastructure\Persistence\Ticket;

use App\Domain\Ticket\Exception\TicketCreateException;
use App\Domain\Ticket\Exception\TicketNotFoundException;
use App\Domain\Ticket\Ticket;
use App\Domain\Ticket\TicketRepository;
use App\Infrastructure\Persistence\Database;
use PDO;

class TicketRepo extends Database implements TicketRepository
{

    public function findAll(): array
    {
        $query = 'SELECT id, name, user_id, is_active  FROM tickets';

        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute();

        $tickets = $stmt->fetchAll();

        if(!$tickets) {
            throw new TicketNotFoundException('No tickets available.');
        }

        $result = [];
        foreach ($tickets as $ticket) {
            $result[] = new Ticket(
                (int) $ticket['id'],
                $ticket['name'],
                $ticket['user_id'],
                $ticket['is_active']
            );
        }
        return $result;
    }

    public function findTicketById(int $ticketId): Ticket
    {
        $query = "SELECT id, name, user_id, is_active FROM tickets WHERE id = :id";

        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindValue('id', $ticketId, PDO::PARAM_INT);
        $stmt->execute();

        $ticket = $stmt->fetch();

        if (!$ticket) {
            throw new TicketNotFoundException();
        }

        return new Ticket(
            (int) $ticket['id'],
            $ticket['name'],
            $ticket['user_id'],
            $ticket['is_active']
        );
    }

    public function createTicket(string $ticketName, int $creatorId): Ticket
    {
        $query = "INSERT INTO tickets (name, user_id) VALUE (:name, :user_id)";

        $stmt = $this->connection->prepare($query);
        $stmt->bindValue('name', $ticketName);
        $stmt->bindValue('user_id', $creatorId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            throw new TicketCreateException();
        }

        return new Ticket(
            (int) $this->connection->lastInsertId(),
            $ticketName,
            $creatorId,
            true
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
