<?php

namespace App\Domain\Section;

class Section implements \JsonSerializable
{
    private $id;
    private $name;
    private $tab_id;
    private $is_active;

    public function __construct(int $id, string $name, int $tab_id, bool $is_active)
    {
        $this->id = $id;
        $this->name = $name;
        $this->tab_id = $tab_id;
        $this->is_active = $is_active;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getTabId()
    {
        return $this->tab_id;
    }

    public function getIsActive()
    {
        return $this->is_active;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'tabId' => $this->tab_id,
            'isActive' => $this->is_active
        ];
    }
}
