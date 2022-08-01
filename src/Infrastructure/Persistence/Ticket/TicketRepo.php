<?php

namespace App\Infrastructure\Persistence\Ticket;

use App\Domain\Ticket\Ticket;
use App\Domain\Ticket\TicketRepository;
use App\Domain\User\UserNoAuthorizationException;
use App\Domain\User\UserNotFoundException;
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

    public function createTicket(string $ticketName, int $creatorId, string $creatorToken): Ticket
    {
        /*
         * Validação se user existe na bd
         * Falta fazer mais validações por exemplo se o User tem role admin
         * etc.
         */
        $validation = $this->checkUserPermission($creatorId, $creatorToken);

        if (!$validation['hasSuccess']) {
            throw new UserNoAuthorizationException($validation['message']);
        }

        $query = "INSERT INTO tickets (name, user_id) VALUE (:name, :user_id)";

        $stmt = $this->connection->prepare($query);
        $stmt->bindValue('name', $creatorToken);
        $stmt->bindValue('user_id', $creatorId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            throw new UserNotFoundException('Error creating ticket');
        }

        return new Ticket(
            (int) $this->connection->lastInsertId(),
            $ticketName,
            $creatorId,
            false
        );
    }

    private function checkUserPermission(int $creatorId, string $creatorToken): array
    {
        $response = [
            'message' => '',
            'hasSuccess' => true
        ];

        $query = 'SELECT * FROM users WHERE 1 = 1 AND id = :id';

        $stmt = $this->connection->prepare($query);
        $stmt->bindValue('id', $creatorId); // default value PDO::PARAM_STR
        $stmt->execute();

        $user = $stmt->fetch();

        if (!$user) {
            $response['message'] = 'User not found, so you are not able to create a ticket';
            $response['hasSuccess'] = false;
            return $response;
        }

        if ($user['password'] !== $creatorToken) {
            $response['message'] = 'User token is wrong';
            $response['hasSuccess'] = false;
            return $response;
        }

        if ($user['role'] !== 'admin') {
            $response['message'] = 'User has no rights to create tickets';
            $response['hasSuccess'] = false;
            return $response;

        }
        if (!$user['isActive']) {
            $response['message'] = 'User is inactive';
            $response['hasSuccess'] = false;
            return $response;
        }

        return $response;
    }

    /*private function rowCount(\PDOStatement $stmt)
    {
        return $stmt->rowCount();
    }*/
}
