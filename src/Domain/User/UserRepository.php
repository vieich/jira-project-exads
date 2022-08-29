<?php
declare(strict_types=1);

namespace App\Domain\User;

interface UserRepository
{

    public function findAll(bool $showHistory): array;
    public function findUserOfId(int $id): User;
    public function createUser(string $name, string $role, string $password): User;
    public function updateUserUsername(int $userId, string $username): User;
    public function updateUserPassword(int $userId, string $oldPassword, string $newPassword): array;
    public function deleteUser(string $username);
    public function createToken(string $username);
    public function checkIfUserExists($username);
}
