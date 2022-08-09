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

    public function findAll(): array
    {
        $query = 'SELECT id, name, ticket_id, is_active FROM tabs';

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
        try {
            $queryCheckTab = 'SELECT name FROM tabs WHERE id = :id AND is_active = true';
            $queryUpdate = "UPDATE tabs SET is_active = false WHERE id = :id";

            $dbConnection = $this->getConnection();
            $dbConnection->beginTransaction();

            $stmt = $dbConnection->prepare($queryCheckTab);
            $stmt->bindValue('id', $tabId, PDO::PARAM_INT);
            $stmt->execute();

            $tabName = $stmt->fetch();

            if (!$tabName) {
                throw new TabNoFoundException();
            }

            $stmt = $this->getConnection()->prepare($queryUpdate);
            $stmt->bindValue('id', $tabId, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() == 0) {
                throw new TabOperationException('Delete tab failed');
            }

            $dbConnection->commit();

            return [
                'message' => 'Tab with id ' . $tabId . ' was deleted',
                'hasSuccess' => true,
            ];
        } catch (TabNoFoundException $e) {
            $dbConnection->rollBack();
            return [
                'message' => $e->getMessage(),
                'hasSuccess' => false,
            ];
        } catch (TabOperationException $e) {
            $dbConnection->rollBack();
            return [
                'message' => $e->getMessage(),
                'hasSuccess' => false,
            ];
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            return [
                'message' => $e->getMessage(),
                'hasSuccess' => false,
            ];
        }
    }

    public function updateTab(int $tabId, string $tabName): Tab
    {
        $this->checkIfTabExist($tabId);

        $query = 'UPDATE tabs SET name = :name WHERE id = :id';
        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindValue('name', $tabName);
        $stmt->bindValue('id', $tabId, PDO::PARAM_INT);
        $stmt->execute();

        if($stmt->rowCount() == 0) {
            throw new TabOperationException('Update tab failed.');
        }

        return $this->findTabById($tabId);
    }

    private function checkIfTabExist(int $tabId)
    {
        $query = 'SELECT name FROM tabs WHERE id = :id AND is_active = true';

        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindValue('id', $tabId, PDO::PARAM_INT);
        $stmt->execute();

        $tab = $stmt->fetch();

        if (!$tab) {
            throw new TabNoFoundException();
        }
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
