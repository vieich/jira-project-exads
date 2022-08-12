<?php

namespace App\Domain\Tab;

interface TabRepository
{
    public function createTab(string $tabName, int $ticketId): Tab;
    public function findAll(bool $showHistory): array;
    public function findTabById(int $tabId): Tab;
    public function deleteTabById(int $tabId): array;
    public function updateTab(int $tabId, string $tabName): Tab;
}