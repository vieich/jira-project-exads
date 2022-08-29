<?php

namespace App\Domain\Section;

interface SectionRepository
{
    public function createSection(string $sectionName, int $tabId): Section;
    public function findAll(bool $showDeleted): array;
    public function findSectionById(int $sectionId): Section;
    public function deleteSection(int $sectionId): array;
    public function updateSection(int $sectionId, string $sectionName): Section;
}