<?php

namespace App\Infrastructure\Persistence\Permission;

use App\Domain\User\UserNoAuthorizationException;
use App\Domain\User\UserNotFoundException;
use App\Infrastructure\Persistence\Database;

class PermissionRepo extends Database
{
    public function checkIfUserPasswordIsCorrect(string $username, string $password): bool
    {
        $query = 'SELECT password FROM users WHERE name = :username';

        $stmt = $this->connection->prepare($query);
        $stmt->bindValue('username', $username);
        $stmt->execute();

        $hashPassword = $stmt->fetch();

        return password_verify($password, $hashPassword['password']);
    }

    public function checkIfUserRoleGivesAccess(string $username, string $httpMethod): bool
    {
        $role = [
            'get' => ['client, admin'],
            'post' => ['admin'],
            'delete' => ['admin'],
            'patch' => ['admin']
        ];

        $query = 'SELECT role FROM users WHERE name = :name';

        $stmt = $this->connection->prepare($query);
        $stmt->bindValue('name', $username);
        $stmt->execute();

        $userRoleFromDb = $stmt->fetch();

        if (!$userRoleFromDb) {
            throw new UserNotFoundException('User not found');
        }

        if (!in_array($userRoleFromDb['role'], $role[$httpMethod])) {
            throw new UserNoAuthorizationException('The user doesnt have rights for that action');
        }
        return true;
    }
}
