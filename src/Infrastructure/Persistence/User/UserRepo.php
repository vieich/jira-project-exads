<?php

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\Exception\UserNoAuthorizationException;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\User;
use App\Domain\User\UserRepository;
use App\Domain\User\UserValidator;
use App\Infrastructure\Persistence\Database;
use PDO;

class UserRepo extends Database implements UserRepository
{
    public function findAll(): array
    {
        $query = 'SELECT id, name, role, isActive, password FROM users';

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
        $query = 'SELECT id, name, role, password, isActive FROM users WHERE id = :id';

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

    public function createUser(string $name, string $role, string $password): User
    {
        $query = 'INSERT INTO users (name, role, password, isActive) VALUE (:name, :role, :password, :isActive)';
        $dbConnection = $this->getConnection();

        try {
            $stmt = $dbConnection->prepare($query);
            $stmt->bindValue('name', strtolower($name));
            $stmt->bindValue('role', strtolower($role));
            $stmt->bindValue('password', $password);
            $stmt->bindValue('isActive', true);
            $stmt->execute();

            $userId = $dbConnection->lastInsertId();

        } catch (\PDOException $e) {
            throw new UserNoAuthorizationException($e->getMessage());
        }

        return new User(
            $userId,
            $name,
            $role,
            true,
            $password
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

    public function updateIsActive(string $username, string $value): array
    {
        $isActive = true;

        if ($value == 'logout') {
            $isActive = false;
        }

        $query = 'UPDATE users SET isActive = :isActive WHERE name = :username ';

        $stmt = $this->connection->prepare($query);
        $stmt->bindValue('isActive', $isActive, PDO::PARAM_BOOL);
        $stmt->bindValue('username', $username);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            throw new UserNotFoundException($query);
        }

        return [
            'hasSuccess' => true,
            'message' => $value . ' successful'
        ];
    }

    public function deleteUser(string $username): array
    {
        $query = "DELETE FROM users WHERE 1 = 1 AND name = :name";

        $stmt = $this->connection->prepare($query);
        $stmt->bindValue('name', $username);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            throw new UserNotFoundException($username . " does not exist");
        }

        return [
            'hasSuccess' => true,
            'message' => $username . ' deleted'
        ];
    }

    public function createToken(string $username): array
    {
        $response = [
            'token' => "",
            "hasSuccess" => true
        ];

        $query = 'SELECT id FROM users WHERE name = :name';
        $query2 = "DELETE FROM tokens WHERE user_id = :userId";
        $query3 = "INSERT INTO tokens (token, user_id) VALUE (:token, :userId)";

        $token = hash('sha256', uniqid());

        $dbConnection = $this->getConnection();
        $dbConnection->beginTransaction();

        try {
            $stmt = $dbConnection->prepare($query);
            $stmt->bindValue('name', $username);
            $stmt->execute();

            $userId = $stmt->fetch()['id'];

            $stmt = $dbConnection->prepare($query2);
            $stmt->bindValue('userId', $userId, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $dbConnection->prepare($query3);
            $stmt->bindValue('token', $token);
            $stmt->bindValue('userId', $userId, PDO::PARAM_INT);
            $stmt->execute();

            $dbConnection->commit();
        } catch (\PDOException $e) {
            $dbConnection->rollBack();
            $response['token'] = $e->getMessage();
            $response['hasSuccess'] = false;
            return $response;
        }
        $response['token'] = $token;

        return $response;
    }

    public function getToken(string $username): array
    {
        $response = [
            'token' => "",
            "hasSuccess" => true
        ];

        $query = 'SELECT t.token FROM tokens t JOIN users u ON u.id = t.user_id WHERE u.name = :username';

        $dbConnection = $this->getConnection();

        $stmt = $dbConnection->prepare($query);
        $stmt->bindValue('username', $username);
        $stmt->execute();

        $token = $stmt->fetch();

        $response['token'] = $token['token'] ?? "";
        $response['hasSuccess'] = $response['token'] != "";

        return $response;
    }
}
