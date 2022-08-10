<?php

namespace App\Infrastructure\Persistence\Item;

use App\Domain\Item\Exception\ItemNoParentSectionException;
use App\Domain\Item\Exception\ItemNotFoundException;
use App\Domain\Item\Exception\ItemOperationException;
use App\Domain\Item\Item;
use App\Domain\Item\ItemRepository;
use App\Infrastructure\Persistence\Database;
use PDO;

class ItemRepo extends Database implements ItemRepository
{

    public function findAll(): array
    {
        $query = 'SELECT i.id, i.name, i.section_id, i.is_active, s.name as statusName FROM items i JOIN status s on s.id = i.status_id';

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
        $query = 'SELECT i.id, i.name, i.section_id, i.is_active, s.name as statusName FROM items i JOIN status s on s.id = i.status_id WHERE i.id = :id';

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

    public function createItem(string $itemName, int $sectionId): Item
    {
        $this->checkIfParentSectionExist($sectionId);

        $query = 'INSERT INTO items (name, section_id) VALUE (:name, :sectionId)';
        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindValue('name', $itemName);
        $stmt->bindValue('sectionId', $sectionId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            throw new ItemOperationException('Failed creating a item for the section with id - ' . $sectionId);
        }

        return new Item(
            (int) $this->getConnection()->lastInsertId(),
            $itemName,
            $sectionId,
            'to do',
            true
        );
    }

    public function deleteItem(int $itemId): array
    {
        $item = $this->findItemById($itemId);

        $query = 'UPDATE items SET is_active = false WHERE id = :id';
        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindValue('id', $itemId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            throw new ItemOperationException('Failed deleting item with id - ' . $itemId);
        }

        return [
            'message' => 'Item with id - ' . $itemId . ' was deleted.',
            'hasSuccess' => true
        ];
    }

    private function checkIfParentSectionExist(int $sectionId)
    {
        $query = 'SELECT name FROM sections WHERE id = :id';

        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindValue('id', $sectionId, PDO::PARAM_INT);
        $stmt->execute();

        $section = $stmt->fetch();

        if (!$section) {
            throw new ItemNoParentSectionException('Section with id - ' . $sectionId . ' not found.');
        }
    }

    public function updateItem(int $itemId, string $name = null, int $statusId = null): Item
    {
        $queryUpdate = 'UPDATE items ';

        $stmt = null;
        $query = "";

        if (!is_null($name) && !is_null($statusId)) {
            $query = $queryUpdate . 'SET name = :name, status_id = :statusId WHERE id = :id';
            $stmt = $this->getConnection()->prepare($query);
            $stmt->bindValue('name', $name);
            $stmt->bindValue('statusId', $statusId, PDO::PARAM_INT);
            $stmt->bindValue('id', $itemId, PDO::PARAM_INT);
        } elseif (!is_null($name)) {
            $query = $queryUpdate . 'SET name = :name WHERE id = :id';
            $stmt = $this->getConnection()->prepare($query);
            $stmt->bindValue('name', $name);
            $stmt->bindValue('id', $itemId, PDO::PARAM_INT);
        } elseif (!is_null($statusId)) {
            $query = $queryUpdate . 'SET status_id = :statusId WHERE id = :id';
            $stmt = $this->getConnection()->prepare($query);
            $stmt->bindValue('name', $name);
            $stmt->bindValue('statusId', $statusId, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $this->findItemById($itemId);
    }
}
