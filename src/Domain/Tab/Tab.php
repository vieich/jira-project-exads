<?php

namespace App\Domain\Tab;

class Tab implements \JsonSerializable
{
    private $id;
    private $name;
    private $ticket_id;
    private $is_active;

    public function __construct(int $id, string $name, int $ticket_id, bool $is_active)
    {
        $this->id = $id;
        $this->name = $name;
        $this->ticket_id = $ticket_id;
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

    public function setName(string $tabName): void
    {
        $this->name = $tabName;
    }

    public function getTicketId(): int
    {
        return $this->ticket_id;
    }

    public function isIsActive(): bool
    {
        return $this->is_active;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'ticketId' => $this->ticket_id,
            'isActive' => $this->is_active
        ];
    }
}