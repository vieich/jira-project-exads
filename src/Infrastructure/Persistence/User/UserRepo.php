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
        $inputValidation = UserValidator::getInstance()->validateUser($name, $role);

        if (!$inputValidation['hasSuccess']) {
            throw new UserNoAuthorizationException($inputValidation['message']);
        }

        $token = UserValidator::getInstance()->createUniqueId();

        $query = 'INSERT INTO users (name, role, password, isActive) VALUE (:name, :role, :password, :isActive)';

        $stmt = $this->connection->prepare($query);
        $stmt->bindValue('name', strtolower($name));
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

    public function updateUser(int $id, string $token = null, string $name = null, string $role = null, bool $isActive = null) :array
    {
        if (!isset($token)) {
            throw new UserNoAuthorizationException('You need to pass the token to make the operation');
        }

        $inputValidation = UserValidator::getInstance()->validateUser($name, $role, $token);

        if (!$inputValidation['hasSuccess']) {
            throw new UserNoAuthorizationException($inputValidation['message']);
        }

        $query = 'UPDATE users SET ';
        $conditionToSelectUser = ' WHERE id = :id AND password = :token';

        if (isset($name) && isset($role) && isset($isActive)) {
            $query .= 'name = :name, role = :role, isActive = :isActive';
        } elseif (isset($name) && isset($role)) {
            $query .= 'name = :name, role = :role';
        } elseif (isset($name) && isset($isActive)) {
            $query .= 'name = :name, isActive = :isActive';
        } elseif (isset($role) && isset($isActive)) {
            $query .= 'role = :role, isActive = :isActive';
        }

        $query .= $conditionToSelectUser;

        $stmt = $this->connection->prepare($query);
        $stmt->bindValue('name', strtolower($name));
        $stmt->bindValue('role', strtolower($role));
        $stmt->bindValue('isActive', $isActive, PDO::PARAM_BOOL);
        $stmt->bindValue('id', $id, PDO::PARAM_INT);
        $stmt->bindValue('token', $token);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            throw new UserNotFoundException($query);
        }

        return [
            'hasSuccess' => true,
            'message' => 'User updated'
        ];
    }

    public function deleteUser(int $id, string $token): array
    {
        // Se houver um ticket associado a um user não conseguimos deletar o user,
        // Temos que usar delete oncascade i guess;

        $inputValidation = UserValidator::getInstance()->validateUser(null, null, $token);

        if (!$inputValidation['hasSuccess']) {
            throw new UserNoAuthorizationException($inputValidation['message']);
        }

        $query = "DELETE FROM users WHERE id = :id AND password = :token";

        $stmt = $this->connection->prepare($query);
        $stmt->bindValue('id', $id, PDO::PARAM_INT);
        $stmt->bindValue('token', $token);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            throw new UserNotFoundException($query);
        }

        return [
            'hasSuccess' => true,
            'message' => 'User deleted'
        ];
    }
}
