<?php

namespace App\Domain\Item;

interface ItemRepository
{
    public function findAll(): array;
    public function findItemById(int $itemId): Item;
    public function createItem(string $itemName, int $sectionId);
    public function deleteItem(int $itemId);
    public function updateItem(string $itemName, int $itemId);
}