<?php

namespace App\Domain\Item;

class Item implements \JsonSerializable
{
    private $id;
    private $name;
    private $sectionId;
    private $status;
    private $isActive;

    public function __construct(int $id, string $name, int $sectionId, string $status, bool $isActive)
    {
        $this->id = $id;
        $this->name = $name;
        $this->sectionId = $sectionId;
        $this->status = $status;
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

    public function getSectionId(): int
    {
        return $this->sectionId;
    }

    public function getStatus(): string
    {
        return $this->status;
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
            'sectionId' => $this->sectionId,
            'status' => $this->status,
            'isActive' => $this->isActive
        ];
    }
}
