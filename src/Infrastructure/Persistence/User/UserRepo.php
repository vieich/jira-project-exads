<?php

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\UserNotFoundException;
use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\Database;
use App\Domain\User\User;
use PDO;

class UserRepo extends Database implements UserRepository
{

    public function findAll(): array
    {
        $query = 'SELECT id, name, role, isActive FROM users';

        $stmt = $this->connection->prepare($query);
        $stmt->execute();

        $users = $stmt->fetchAll();

        if (!$users) {
            throw new UserNotFoundException('There are no users available');
        }

        $result = [];
        foreach ($users as $user) {
            $result[] = new User(
                $user['id'],
                $user['name'],
                $user['role'],
                $user['isActive']
            );
        }
        return $result;
    }

    public function findUserOfId(int $id): User
    {
        $query = 'SELECT id, name, role, isActive FROM users WHERE 1 = 1 AND id=:id';

        $stmt = $this->connection->prepare($query);
        $stmt->bindValue('id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $user = $stmt->fetch();

        if (!$user) {
            throw new UserNotFoundException();
        }

        return new User(
            $user['id'],
            $user['name'],
            $user['role'],
            $user['isActive']
        );
    }
}
