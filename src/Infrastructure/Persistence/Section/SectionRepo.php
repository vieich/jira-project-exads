<?php

namespace App\Infrastructure\Persistence\Section;

use App\Domain\Section\Exception\SectionNoParentException;
use App\Domain\Section\Exception\SectionNotFoundException;
use App\Domain\Section\Exception\SectionOperationException;
use App\Domain\Section\Section;
use App\Domain\Section\SectionRepository;
use App\Infrastructure\Persistence\Database;
use PDO;

class SectionRepo extends Database implements SectionRepository
{

    /**
     * @throws SectionOperationException
     * @throws SectionNoParentException
     */
    public function createSection(string $sectionName, int $tabId): Section
    {
        $this->checkIfParentTabExists($tabId);

        $query = 'INSERT INTO sections (name, tab_id, is_active) VALUE (:name, :tabId, :isActive)';
        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindValue('name', $sectionName);
        $stmt->bindValue('tabId', $tabId, PDO::PARAM_INT);
        $stmt->bindValue('isActive', true, PDO::PARAM_BOOL);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            throw new SectionOperationException('Failed section creation');
        }

        return new Section(
            (int) $this->getConnection()->lastInsertId(),
            $sectionName,
            $tabId,
            true
        );
    }

    /**
     * @throws SectionNotFoundException
     */
    public function findAll(bool $showDeleted): array
    {
        $query = 'SELECT id, name, tab_id, is_active FROM sections';

        if (!$showDeleted) {
            $query .= ' WHERE is_active = true';
        }

        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute();

        $sections = $stmt->fetchAll();

        if (!$sections) {
            throw new SectionNotFoundException();
        }

        $result = [];

        foreach ($sections as $section) {
            $result[] = new Section(
                (int) $section['id'],
                $section['name'],
                (int) $section['tab_id'],
                $section['is_active']
            );
        }
        return $result;
    }

    /**
     * @throws SectionNotFoundException
     */
    public function findSectionById(int $sectionId): Section
    {
        $query = 'SELECT id, name, tab_id, is_active FROM sections WHERE id = :id AND is_active = true';
        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindValue('id', $sectionId, PDO::PARAM_INT);
        $stmt->execute();

        $section = $stmt->fetch();

        if (!$section) {
            throw new SectionNotFoundException('Section does not exist.');
        }

        return new Section(
            (int) $section['id'],
            $section['name'],
            (int) $section['tab_id'],
            $section['is_active']
        );
    }

    /**
     * @throws SectionNotFoundException
     * @throws SectionOperationException
     */
    public function deleteSection(int $sectionId): array
    {
        $this->findSectionById($sectionId);

        $query = 'UPDATE sections SET is_active = false WHERE id = :id';

        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindValue('id', $sectionId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            throw new SectionOperationException('Delete section failed.');
        }

        return [
            'message' => 'Section with id ' . $sectionId . ' was deleted',
            'hasSuccess' => true
        ];
    }

    /**
     * @throws SectionOperationException
     * @throws SectionNotFoundException
     */
    public function updateSection(int $sectionId, string $sectionName): Section
    {
        $section = $this->findSectionById($sectionId);

        $query = 'UPDATE sections SET name = :name WHERE id = :id';

        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindValue('id', $sectionId, PDO::PARAM_INT);
        $stmt->bindValue('name', $sectionName);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            throw new SectionOperationException('Update section failed, nothing to update');
        }

        $section->setName($sectionName);

        return $section;
    }

    /**
     * @throws SectionNoParentException
     */
    private function checkIfParentTabExists(int $tabId)
    {
        $query = 'SELECT name FROM tabs WHERE id = :id AND is_active = true';

        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindValue('id', $tabId, PDO::PARAM_INT);
        $stmt->execute();

        $tab = $stmt->fetch();

        if (!$tab) {
            throw new SectionNoParentException();
        }
    }
}
