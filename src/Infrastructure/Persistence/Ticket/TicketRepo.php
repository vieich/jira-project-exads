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

        if (count($validation) > 1) {
            throw new UserNoAuthorizationException($validation['message']);
        }

        $user_id = $validation['user_id'];

        $query = "INSERT INTO tickets (name, user_id) VALUE (:name, :user_id)";

        $stmt = $this->connection->prepare($query);
        $stmt->bindValue('name', $creatorToken);
        $stmt->bindValue('user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            throw new UserNotFoundException('Error creating ticket');
        }

        return new Ticket(
            (int) $this->connection->lastInsertId(),
            $ticketName,
            $user_id,
            false
        );
    }

    private function checkUserPermission(int $creatorId, string $creatorToken): array
    {
        $user_id = 0;
        $message = "";

        $query = 'SELECT * FROM users WHERE 1 = 1 AND id = :id';

        $stmt = $this->connection->prepare($query);
        $stmt->bindValue('id', $creatorId); // default value PDO::PARAM_STR
        $stmt->execute();

        $user = $stmt->fetch();

        if (!$user) {
            $message = "User not found, so you are not able to create a ticket";
            return ['user_id' => $user_id,
                'message' => $message];
        }
        if ($user['password'] !== $creatorToken) {
            $message = "User token is wrong";
            return ['user_id' => $user_id,
                'message' => $message];
        }
        if ($user['role'] !== 'admin') {
            $message = "User has no rights to create tickets";
            return ['user_id' => $user_id,
                'message' => $message];
        }
        if (!$user['isActive']) {
            $message = "User is inactive";
            return ['user_id' => $user_id,
                'message' => $message];
        }

        return ['user_id' => $user['id']];
    }

    /*private function rowCount(\PDOStatement $stmt)
    {
        return $stmt->rowCount();
    }*/
}
