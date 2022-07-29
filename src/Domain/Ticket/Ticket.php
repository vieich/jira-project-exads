<?php

namespace App\Domain\Ticket;

use App\Domain\User\User;

class Ticket implements \JsonSerializable
{
    private $id;
    private $name;
    private $user;
    private $isDone;
    private $createdAt;

    public function __construct(int $id, string $name, int $user, bool $isDone)
    {
        $this->id = $id;
        $this->name = $name;
        $this->user = $user;
        $this->isDone = $isDone;
        $this->createdAt = time();
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

    public function isDone(): bool
    {
        return $this->isDone;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }



    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'user_id' => $this->user,
            'isDone' => $this->isDone,
        ];
    }
}
