<?php

namespace App\Application\Actions\Tab;

use App\Application\Actions\Action;
use App\Domain\Tab\TabPaginator;
use App\Domain\Tab\TabRepository;
use App\Domain\Tab\TabValidator;
use App\Infrastructure\Persistence\Permission\PermissionRepo;
use Psr\Log\LoggerInterface;

abstract class TabAction extends Action
{
    protected $tabRepository;
    protected $permissionRepository;
    protected $tabValidator;
    protected $tabPaginator;

    public function __construct(
        LoggerInterface $logger,
        TabRepository $tabRepository,
        PermissionRepo $permissionRepo,
        TabValidator $tabValidator,
        TabPaginator $tabPaginator
    ) {
        parent::__construct($logger);
        $this->tabRepository = $tabRepository;
        $this->permissionRepository = $permissionRepo;
        $this->tabValidator = $tabValidator;
        $this->tabPaginator = $tabPaginator;
    }
}
