<?php

namespace App\Domain\Ticket;

class Ticket implements \JsonSerializable
{
    private $id;
    private $name;
    private $user;
    private $isActive;

    public function __construct(int $id, string $name, int $user, bool $isActive)
    {
        $this->id = $id;
        $this->name = $name;
        $this->user = $user;
        $this->isActive = $isActive;
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUser(): int
    {
        return $this->user;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'userId' => $this->user,
            'isActive' => $this->isActive
        ];
    }
}
