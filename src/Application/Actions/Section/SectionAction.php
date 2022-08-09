<?php

namespace App\Application\Actions\Section;

use App\Application\Actions\Action;
use App\Domain\Section\SectionRepository;
use App\Infrastructure\Persistence\Permission\PermissionRepo;
use Psr\Log\LoggerInterface;

abstract class SectionAction extends Action
{
    protected $sectionRepository;
    protected $permissionRepository;
    protected $sectionValidator;

    public function __construct(LoggerInterface $logger, SectionRepository $sectionRepository, PermissionRepo $permissionRepo, SectionValidator $sectionValidator)
    {
        parent::__construct($logger);
        $this->sectionRepository = $sectionRepository;
        $this->permissionRepository = $permissionRepo;
        $this->sectionValidator = $sectionValidator;
    }
}