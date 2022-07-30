<?php

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\UserNoAuthorizationException;
use App\Domain\User\UserNotFoundException;
use App\Domain\User\UserRepository;
use App\Domain\User\UserValidator;
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
                $user['isActive'],
                $user['password']
            );
        }
        return $result;
    }

    public function findUserOfId(int $id): User
    {
        $query = 'SELECT id, name, role, isActive, password FROM users WHERE 1 = 1 AND id=:id';

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
            $user['isActive'],
            $user['password']
        );
    }

    public function createUser(string $name, string $role, bool $isActive): User
    {
        $inputValidation = UserValidator::getInstance()->validateUser($name,$role,$isActive);

        if(!$inputValidation['hasSuccess']) {
            throw new UserNoAuthorizationException($inputValidation['message']);
        }

        $token = UserValidator::getInstance()->createUniqueId();

        $query = 'INSERT INTO users (name, role, password, isActive, updateAt) VALUE (:name, :role, :password, :isActive, now())';

        $stmt = $this->connection->prepare($query);
        $stmt->bindValue('name', $name);
        $stmt->bindValue('role', strtolower($role));
        $stmt->bindValue('password', $token);
        $stmt->bindValue('isActive', $isActive);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            throw new UserNotFoundException('Error creating User');
        }

        return new User(
            (int) $this->connection->lastInsertId(),
            $name,
            $role,
            $isActive,
            $token
        );
    }
}
