<?php

namespace App\Application\Actions\Items;

use App\Application\Actions\Action;
use App\Domain\Item\ItemRepository;
use App\Domain\Item\ItemValidator;
use App\Infrastructure\Persistence\Permission\PermissionRepo;
use Psr\Log\LoggerInterface;

abstract class ItemAction extends Action
{
    protected $itemRepository;
    protected $permissionRepo;
    protected $itemValidator;

    /**
     * @param LoggerInterface $logger
     * @param ItemRepository $itemRepository
     * @param PermissionRepo $permissionRepo
     * @param ItemValidator $itemValidator
     */
    public function __construct(LoggerInterface $logger, ItemRepository $itemRepository, PermissionRepo $permissionRepo, ItemValidator $itemValidator)
    {
        parent::__construct($logger);
        $this->itemRepository = $itemRepository;
        $this->permissionRepo = $permissionRepo;
        $this->itemValidator = $itemValidator;
    }
}
