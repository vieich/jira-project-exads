<?php

namespace App\Infrastructure\Persistence\Tab;

use App\Domain\Tab\Exception\TabCreationException;
use App\Domain\Tab\Exception\TabNoParentTicketException;
use App\Domain\Tab\Tab;
use App\Domain\Tab\TabRepository;
use App\Infrastructure\Persistence\Database;
use PDO;

class TabRepo extends Database implements TabRepository
{
    public function createTab(string $tabName, int $ticketId): Tab
    {
        $queryTicketExist = 'SELECT name FROM tickets WHERE id = :id';

        $stmt = $this->getConnection()->prepare($queryTicketExist);
        $stmt->bindValue('id', $ticketId);
        $stmt->execute();

        $ticket = $stmt->fetch();

        if(!$ticket) {
            throw new TabNoParentTicketException();
        }

        $queryCreateTab = 'INSERT INTO tabs (name, ticket_id, is_active, is_done) VALUE (:name, :ticketId, :isActive, :isDone)';

        $stmt = $this->getConnection()->prepare($queryCreateTab);
        $stmt->bindValue('name', $tabName);
        $stmt->bindValue('ticketId', $ticketId, PDO::PARAM_INT);
        $stmt->bindValue('isActive', true, PDO::PARAM_BOOL);
        $stmt->bindValue('isDone', false, PDO::PARAM_BOOL);
        $stmt->execute();

        if($stmt->rowCount() == 0) {
            throw new TabCreationException();
        }

        return new Tab(
            (int) $this->getConnection()->lastInsertId(),
            $tabName,
            $ticketId,
            false,
            true
        );
    }
}