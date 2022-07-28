<?php
declare(strict_types=1);

namespace App\Domain\User;

use JsonSerializable;

class User implements JsonSerializable
{
    private $id;
    private $name;
    private $role;
    private $isActive;

    public function __construct(int $id, string $name, string $role, bool $isActive)
    {
        $this->id = $id;
        $this->name = strtolower($name);
        $this->role = ucfirst($role);
        $this->isActive = $isActive;
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
        ];
    }
}
