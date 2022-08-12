<?php

namespace App\Infrastructure\Persistence\Tab;

use App\Domain\Tab\Exception\TabOperationException;
use App\Domain\Tab\Exception\TabNoFoundException;
use App\Domain\Tab\Exception\TabNoParentTicketException;
use App\Domain\Tab\Tab;
use App\Domain\Tab\TabRepository;
use App\Infrastructure\Persistence\Database;
use PDO;

class TabRepo extends Database implements TabRepository
{
    public function createTab(string $tabName, int $ticketId): Tab
    {
        $this->checkIfParentTicketExist($ticketId);

        $queryCreateTab = 'INSERT INTO tabs (name, ticket_id, is_active) VALUE (:name, :ticketId, :isActive)';

        $stmt = $this->getConnection()->prepare($queryCreateTab);
        $stmt->bindValue('name', $tabName);
        $stmt->bindValue('ticketId', $ticketId, PDO::PARAM_INT);
        $stmt->bindValue('isActive', true, PDO::PARAM_BOOL);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            throw new TabOperationException('Create tab failed.');
        }

        return new Tab(
            (int) $this->getConnection()->lastInsertId(),
            $tabName,
            $ticketId,
            true
        );
    }

    public function findAll(bool $showHistory): array
    {
        $query = 'SELECT id, name, ticket_id, is_active FROM tabs';

        if(!$showHistory) {
            $query .= ' WHERE is_active = true';
        }

        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute();

        $tabs = $stmt->fetchAll();

        if (!$tabs) {
            throw new TabNoFoundException('No tabs available to show.');
        }

        $result = [];

        foreach ($tabs as $tab) {
            $result[] = new Tab(
                $tab['id'],
                $tab['name'],
                $tab['ticket_id'],
                $tab['is_active']
            );
        }
        return $result;
    }

    public function findTabById(int $tabId): Tab
    {
        $query = 'SELECT id, name, ticket_id, is_active FROM tabs WHERE id = :id AND is_active = true';

        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindValue('id', $tabId, PDO::PARAM_INT);
        $stmt->execute();

        $tab = $stmt->fetch();

        if (!$tab) {
            throw new TabNoFoundException();
        }

        return new Tab(
            $tab['id'],
            $tab['name'],
            $tab['ticket_id'],
            $tab['is_active']
        );
    }

    public function deleteTabById(int $tabId): array
    {

        $this->findTabById($tabId);

        $queryUpdate = "UPDATE tabs SET is_active = false WHERE id = :id";

        $stmt = $this->getConnection()->prepare($queryUpdate);
        $stmt->bindValue('id', $tabId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            throw new TabOperationException('Delete tab failed');
        }

        return [
            'message' => 'Tab with id ' . $tabId . ' was deleted',
            'hasSuccess' => true
        ];
    }

    public function updateTab(int $tabId, string $tabName): Tab
    {
        $tab = $this->findTabById($tabId);

        $query = 'UPDATE tabs SET name = :name WHERE id = :id';
        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindValue('name', $tabName);
        $stmt->bindValue('id', $tabId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            throw new TabOperationException('Update tab failed.');
        }

        $tab->setName($tabName);
        return $tab;
    }

    private function checkIfParentTicketExist(int $ticketId)
    {
        $queryTicketExist = 'SELECT name FROM tickets WHERE id = :id';

        $stmt = $this->getConnection()->prepare($queryTicketExist);
        $stmt->bindValue('id', $ticketId);
        $stmt->execute();

        $ticket = $stmt->fetch();

        if (!$ticket) {
            throw new TabNoParentTicketException();
        }
    }
}
