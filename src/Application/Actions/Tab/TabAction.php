<?php

namespace App\Application\Actions\Tab;

use App\Application\Actions\Action;
use App\Domain\Tab\TabRepository;
use App\Infrastructure\Persistence\Permission\PermissionRepo;
use Psr\Log\LoggerInterface;

abstract class TabAction extends Action
{
    protected $tabRepository;
    protected $permissionRepository;

    public function __construct(LoggerInterface $logger, TabRepository $tabRepository, PermissionRepo $permissionRepo)
    {
        parent::__construct($logger);
        $this->tabRepository = $tabRepository;
        $this->permissionRepository = $permissionRepo;
    }
}