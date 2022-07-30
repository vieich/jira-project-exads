<?php
declare(strict_types=1);

namespace App\Domain\User;

use JsonSerializable;

class User implements JsonSerializable
{
    private $id;
    private $name;
    private $role;
    private $token;
    private $isActive;

    public function __construct(int $id, string $name, string $role, bool $isActive, string $token)
    {
        $this->id = $id;
        $this->name = $name;
        $this->role = $role;
        $this->isActive = $isActive;
        $this->token = $token;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'role' => $this->role,
            'isActive' => $this->isActive,
            'token' => $this->token
        ];
    }
}
