<?php

namespace App\Infrastructure\Persistence\Ticket;

use App\Domain\Ticket\Exception\TicketCreateException;
use App\Domain\Ticket\Exception\TicketNotFoundException;
use App\Domain\Ticket\Exception\TicketOperationException;
use App\Domain\Ticket\Exception\TicketPayloadDataException;
use App\Domain\Ticket\Ticket;
use App\Domain\Ticket\TicketRepository;
use App\Infrastructure\Persistence\Database;
use PDO;

class TicketRepo extends Database implements TicketRepository
{

    public function findAll(bool $showHistory): array
    {
        $query = 'SELECT id, name, user_id, is_active  FROM tickets';

        if (!$showHistory) {
            $query .= ' WHERE is_active = true';
        }

        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute();

        $tickets = $stmt->fetchAll();

        if (!$tickets) {
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
        $query = "SELECT id, name, user_id, is_active FROM tickets WHERE id = :id AND is_active = true";

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

    public function deleteTicket(int $ticketId): array
    {
        $this->findTicketById($ticketId);

        $query = 'UPDATE tickets SET is_active = false WHERE id = :id';

        $stmt = $this->connection->prepare($query);
        $stmt->bindValue('id', $ticketId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            throw new TicketOperationException('Failed to delete Ticket with id ' . $ticketId);
        }

        return [
            'message' => 'Ticket with id ' . $ticketId . ' deleted',
            'hasSuccess' => true
        ];
    }

    public function updateTicket(int $ticketId, string $ticketName): Ticket
    {
        $ticket = $this->findTicketById($ticketId);

        $query = 'UPDATE tickets SET name = :name WHERE id = :id';
        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindValue('name', $ticketName);
        $stmt->bindValue('id', $ticketId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            throw new TicketOperationException('Failed updating ticket.');
        }

        return new Ticket(
            $ticketId,
            $ticketName,
            $ticket->getUser(),
            $ticket->getIsActive()
        );
    }
}
