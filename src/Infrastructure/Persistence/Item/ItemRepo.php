<?php

namespace App\Infrastructure\Persistence\Item;

use App\Domain\Item\Exception\ItemNotFoundException;
use App\Domain\Item\Item;
use App\Domain\Item\ItemRepository;
use App\Infrastructure\Persistence\Database;
use PDO;

class ItemRepo extends Database implements ItemRepository
{

    public function findAll(): array
    {
        $query = 'SELECT i.id, i.name, i.section_id, i.is_active, s.name as statusName FROM items i JOIN items_status i_s on i.id = i_s.item_id JOIN status s on s.id = i_s.status_id';

        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute();

        $items = $stmt->fetchAll();

        if (!$items) {
            throw new ItemNotFoundException();
        }

        $result = [];

        foreach ($items as $item) {
            $result[] = new Item(
                (int) $item['id'],
                $item['name'],
                (int) $item['section_id'],
                $item['statusName'],
                $item['is_active']
            );
        }
        return $result;
    }

    public function findItemById(int $itemId): Item
    {
        $query = 'SELECT i.id, i.name, i.section_id, i.is_active, s.name as statusName FROM items i JOIN items_status i_s on i.id = i_s.item_id JOIN status s on s.id = i_s.status_id WHERE i.id = :id';

        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindValue('id', $itemId, PDO::PARAM_INT);
        $stmt->execute();

        $item = $stmt->fetch();

        if (!$item) {
            throw new ItemNotFoundException();
        }

        return new Item(
            $item['id'],
            $item['name'],
            $item['section_id'],
            $item['statusName'],
            $item['is_active']
        );
    }

    public function createItem(string $itemName, int $sectionId)
    {
        //check if section exists
    }

    public function deleteItem(int $itemId)
    {
        // TODO: Implement deleteItem() method.
    }

    public function updateItem(string $itemName, int $itemId)
    {
        // TODO: Implement updateItem() method.
    }
}
