<?php

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\Database;
use App\Domain\User\User;
use PDO;

class UserRepo extends Database implements UserRepository
{


    public function findAll(): array
    {
        $users = $this->getConnection()
            ->query('SELECT id, name, role, isActive FROM users')
            ->fetchAll(PDO::FETCH_ASSOC);

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
        return new User(1, "teste", "teste", "teste");
    }
}
