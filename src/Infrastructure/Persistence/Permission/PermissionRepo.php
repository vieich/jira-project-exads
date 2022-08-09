<?php

namespace App\Infrastructure\Persistence\Permission;

use App\Domain\Permission\Exception\PermissionAuthTokenException;
use App\Domain\User\Exception\UserNoAuthorizationException;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\User;
use App\Infrastructure\Persistence\Database;
use PDO;

class PermissionRepo extends Database
{

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
        $tokenFromDb = $this->checkIfTokenIsExpired($token);

        if (!$tokenFromDb['hasSuccess']) {
            throw new PermissionAuthTokenException('Log in to get an valid auth token.');
        }

        if ($token != $tokenFromDb['token']) {
            throw new PermissionAuthTokenException('Token is not valid.');
        }
    }

    public function getAuthToken(string $username): array
    {
        $query = 'SELECT t.token FROM tokens t JOIN users u on t.user_id = u.id WHERE u.name = :name';

        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindValue('name', $username);
        $stmt->execute();

        $tokenFromDb = $stmt->fetch();

        if (!$tokenFromDb) {
            $token = $this->createToken($username);
            if (!$token['hasSuccess']) {
                throw new PermissionAuthTokenException('Error while trying to create an Auth Token');
            }
            return $token;
        }

        $responseIsTokenValid = $this->checkIfTokenIsExpired($tokenFromDb['token']);

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

        $timeOfLife = 20;

        $query = 'SELECT token FROM tokens WHERE user_id = :user_id AND create_at + INTERVAL ' . $timeOfLife . ' MINUTE > now()';

        try {
            $user = $this->getUserByToken($token);

            $stmt = $this->getConnection()->prepare($query);
            $stmt->bindValue('user_id', $user->getId(), PDO::PARAM_INT);
            $stmt->execute();

            $tokenFromDb = $stmt->fetch();

            if ($tokenFromDb['token']) {
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
        $query = 'SELECT u.id, u.name, u.role, u.password , u.is_active FROM users u JOIN tokens t on u.id = t.user_id WHERE t.token = :token';

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
            $user['is_active'],
            $user['password']
        );
    }
}
