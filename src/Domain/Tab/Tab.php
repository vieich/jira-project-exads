<?php

namespace App\Domain\Tab;

class Tab implements \JsonSerializable
{
    private $id;
    private $name;
    private $ticket_id;
    private $is_done;
    private $is_active;

    public function __construct(int $id, string $name, int $ticket_id, bool $is_done, bool $is_active)
    {
        $this->id = $id;
        $this->name = $name;
        $this->ticket_id = $ticket_id;
        $this->is_done = $is_done;
        $this->is_active = $is_active;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTicketId(): int
    {
        return $this->ticket_id;
    }

    public function isIsDone(): bool
    {
        return $this->is_done;
    }

    public function isIsActive(): bool
    {
        return $this->is_active;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'ticket_id' => $this->ticket_id,
            'is_done' => $this->is_done,
            'is_active' => $this->is_active
        ];
    }
}