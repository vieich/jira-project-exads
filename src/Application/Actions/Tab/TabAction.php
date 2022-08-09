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
    protected $tabValidator;

    public function __construct(LoggerInterface $logger, TabRepository $tabRepository, PermissionRepo $permissionRepo, TabValidator $tabValidator)
    {
        parent::__construct($logger);
        $this->tabRepository = $tabRepository;
        $this->permissionRepository = $permissionRepo;
        $this->tabValidator = $tabValidator;
    }
}