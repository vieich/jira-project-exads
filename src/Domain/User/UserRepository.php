<?php
declare(strict_types=1);

namespace App\Domain\User;

interface UserRepository
{

    public function findAll(): array;
    public function findUserOfId(int $id): User;
    public function createUser(string $name, string $role, string $password);
    public function updateUser(int $id, string $token, string $name, string $role, bool $isActive);
    public function deleteUser(string $username);
    public function updateIsActive(string $username, string $value);
    public function getToken(string $username);
    public function createToken(string $username);
}
