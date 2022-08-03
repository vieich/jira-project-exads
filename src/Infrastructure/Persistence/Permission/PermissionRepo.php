<?php

namespace App\Infrastructure\Persistence\Permission;

use App\Domain\Permission\Exception\PermissionAuthTokenException;
use App\Domain\Permission\Exception\PermissionLoginException;
use App\Domain\User\Exception\UserNoAuthorizationException;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\User;
use App\Infrastructure\Persistence\Database;
use PDO;

class PermissionRepo extends Database
{
    public function checkIfUserPasswordIsCorrect(string $username, string $password): void
    {
        $query = 'SELECT password FROM users WHERE name = :username';

        $stmt = $this->connection->prepare($query);
        $stmt->bindValue('username', $username);
        $stmt->execute();

        $hashPassword = $stmt->fetch();

        if (!password_verify($password, $hashPassword['password'])) {
            throw new PermissionLoginException();
        }
    }

    public function checkIfUserCanDoOperation(string $token, string $operation): void
    {
        $role = [
            'read' => ['client', 'admin'],
            'create' => ['admin'],
            'delete' => ['admin'],
            'update' => ['admin']
        ];

        $query = 'SELECT role FROM users WHERE name = :name';

        try {
            $user = $this->getUserByToken($token);

            $stmt = $this->connection->prepare($query);
            $stmt->bindValue('name', $user->getName());
            $stmt->execute();

            $userRoleFromDb = $stmt->fetch();

            if (!$userRoleFromDb) {
                throw new UserNotFoundException('User not found');
            }

            if (!in_array($userRoleFromDb['role'], $role[$operation])) {
                throw new UserNoAuthorizationException('You have no permission for this operation');
            }
        } catch (\PDOException $e) {
            throw new UserNotFoundException();
        }
    }

    public function checkIfAuthTokenIsValid(string $token): void
    {
        $result = $this->checkIfTokenIsExpired($token);

        if (!$result['hasSuccess']) {
            throw new PermissionAuthTokenException('Log in to get an valid auth token.');
        }

        if ($token != $result['token']) {
            throw new PermissionAuthTokenException('Token is not valid.');
        }
    }

    public function getAuthToken(string $username): array
    {
        $responseIsTokenValid = $this->checkIfTokenIsExpired($username);

        if (!$responseIsTokenValid['hasSuccess']) {
            $token = $this->createToken($username);
            if (!$token['hasSuccess']) {
                throw new PermissionAuthTokenException('Error while trying to create an Auth Token');
            }
            return $token;
        }
        return $responseIsTokenValid;
    }

    public function checkIfTokenIsExpired(string $token): array
    {
        $response = [
            "token" => "",
            "hasSuccess" => false
        ];

        $timeOfLife = 5;

        $query = 'SELECT token FROM tokens WHERE user_id = :user_id AND createAt + INTERVAL ' . $timeOfLife . ' MINUTE > now()';

        try {
            $user = $this->getUserByToken($token);

            $stmt = $this->getConnection()->prepare($query);
            $stmt->bindValue('user_id', $user->getId(), PDO::PARAM_INT);
            $stmt->execute();

            $tokenFromDb = $stmt->fetch();

            if (isset($tokenFromDb['token'])) {
                $response['token'] = $tokenFromDb['token'];
                $response['hasSuccess'] = true;

                return $response;
            }
            return $response;
        } catch (\PDOException $e) {
            $response['token'] = $e->getMessage();
            return $response;
        } catch (\Exception $e) {
            $response['token'] = $e->getMessage();
            return $response;
        }
    }

    private function createToken(string $username): array
    {
        $response = [
            "token" => "",
            "hasSuccess" => false
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
            return $response;
        }
        $response['token'] = $token;
        $response['hasSuccess'] = true;
        return $response;
    }

    public function getUserByToken(string $token): User
    {
        $query = 'SELECT u.id, u.name, u.role, u.isActive, u.password FROM users u JOIN tokens t on u.id = t.user_id WHERE t.token = :token';

        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindValue('token', $token);
        $stmt->execute();

        $user = $stmt->fetch();

        if (!$user) {
            throw new PermissionAuthTokenException('Auth token is not valid, log in to get a new one.');
        }

        return new User(
            (int) $user['id'],
            $user['name'],
            $user['role'],
            $user['isActive'],
            $user['password']
        );
    }
}
