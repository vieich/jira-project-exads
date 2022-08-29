<?php

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\Exception\UserNoAuthorizationException;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\Exception\UserOperationException;
use App\Domain\User\User;
use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\Database;
use PDO;

class UserRepo extends Database implements UserRepository
{
    /**
     * @throws UserNotFoundException
     */
    public function findAll(bool $showHistory): array
    {
        $query = 'SELECT id, name, role, is_active, password FROM users';

        if (!$showHistory) {
            $query .= ' WHERE is_active = true';
        }

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
                $user['is_active'],
                $user['password']
            );
        }
        return $result;
    }

    /**
     * @throws UserNotFoundException
     */
    public function findUserOfId(int $id): User
    {
        $query = 'SELECT id, name, role, password, is_active FROM users WHERE id = :id AND is_active = true';

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
            $user['is_active'],
            $user['password']
        );
    }

    /**
     * @throws UserOperationException
     */
    public function createUser(string $name, string $role, string $password): User
    {
        $query = 'INSERT INTO users (name, role, password, is_active) VALUE (:name, :role, :password, :isActive)';
        $dbConnection = $this->getConnection();

        try {
            $stmt = $dbConnection->prepare($query);
            $stmt->bindValue('name', $name);
            $stmt->bindValue('role', strtolower($role));
            $stmt->bindValue('password', $password);
            $stmt->bindValue('isActive', true);
            $stmt->execute();

            $userId = $dbConnection->lastInsertId();
        } catch (\PDOException $e) {
            throw new UserOperationException('Username already exists');
        }

        return new User(
            $userId,
            $name,
            $role,
            true,
            $password
        );
    }

    /**
     * @throws UserOperationException
     * @throws UserNotFoundException
     */
    public function deleteUser(string $username): array
    {
        $user = $this->checkIfUserExists($username);

        $query = "UPDATE users SET name = :username , is_active = false WHERE name = :name";

        $stmt = $this->connection->prepare($query);
        $stmt->bindValue('name', $username);
        $stmt->bindValue('username', $username . 'id' . $user->getId());
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            throw new UserOperationException('Failed deleting user with username' . $username);
        }

        return [
            'message' => $username . ' deleted',
            'hasSuccess' => true,
        ];
    }

    public function deleteToken(int $userId)
    {
        $queryDeleteToken = "DELETE FROM tokens WHERE user_id = :userId";

        $stmt = $this->getConnection()->prepare($queryDeleteToken);
        $stmt->bindValue('userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function createToken(string $username): array
    {
        $response = [
            'token' => "",
            "hasSuccess" => true
        ];

        $queryUserId = 'SELECT id FROM users WHERE name = :name AND is_active = true';
        $queryCreateToken = "INSERT INTO tokens (token, user_id) VALUE (:token, :userId)";

        $token = hash('sha256', uniqid());

        $dbConnection = $this->getConnection();
        $dbConnection->beginTransaction();

        try {
            $stmt = $dbConnection->prepare($queryUserId);
            $stmt->bindValue('name', $username);
            $stmt->execute();

            $userId = $stmt->fetch()['id'];

            $this->deleteToken($userId);

            $stmt = $dbConnection->prepare($queryCreateToken);
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

    /**
     * @throws UserNotFoundException
     * @throws UserNoAuthorizationException
     */
    public function checkIfUserPasswordIsCorrect(string $username, string $password): void
    {
        $query = 'SELECT password FROM users WHERE name = :username AND is_active = true';
        try {
            $stmt = $this->connection->prepare($query);
                $stmt->bindValue('username', $username);
                $stmt->execute();

                $hashPassword = $stmt->fetch();
        } catch (\PDOException $e) {
            throw new UserNotFoundException('Wrong username or password.');
        }
        if (!password_verify($password, $hashPassword['password'])) {
            throw new UserNoAuthorizationException('Wrong username or password.');
        }
    }

    /**
     * @throws UserNotFoundException
     */
    public function checkIfUserExists($username): User
    {
        $query = "SELECT id, name, role, is_active, password FROM users WHERE name = :name AND is_active = true";
        try {
            $stmt = $this->getConnection()->prepare($query);
            $stmt->bindValue('name', $username);
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new UserNotFoundException('Usefdsfdsfdkjshfjkdshfr ' . $username . ' does not exist.');
        }
        $user = $stmt->fetch();

        if (!$user) {
            throw new UserNotFoundException('User ' . $username . ' does not exist.');
        }

        return new User(
            $user['id'],
            $user['name'],
            $user['role'],
            $user['is_active'],
            $user['password']
        );
    }

    public function updateUserUsername(int $userId, string $username): User
    {
        $query = 'UPDATE users SET name = :name WHERE id = :id';

        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindValue('name', $username);
        $stmt->bindValue('id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $this->findUserOfId($userId);
    }

    public function updateUserPassword(int $userId, string $oldPassword, string $newPassword): array
    {
        $user = $this->findUserOfId($userId);
        $this->checkIfUserPasswordIsCorrect($user->getName(), $oldPassword);

        $hashPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $query = 'UPDATE users SET password = :password WHERE id = :id';
        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindValue('password', $hashPassword);
        $stmt->bindValue('id', $userId);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            throw new UserOperationException('Update password failed.');
        }

        $this->deleteToken($userId);

        return [
            'message' => $user->getName() . ' password was successfully updated.',
            'hasSuccess' => true
        ];
    }
}
