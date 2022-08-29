<?php

namespace App\Domain\Item;

interface ItemRepository
{
    public function findAll(bool $showDeleted): array;
    public function findItemById(int $itemId): Item;
    public function createItem(string $itemName, int $sectionId): Item;
    public function deleteItem(int $itemId);
    public function updateItem(int $itemId, string $name = null, int $statusId = null): Item;
}