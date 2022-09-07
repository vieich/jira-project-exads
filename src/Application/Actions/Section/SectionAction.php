<?php

namespace App\Application\Actions\Section;

use App\Application\Actions\Action;
use App\Domain\Section\SectionPaginator;
use App\Domain\Section\SectionRepository;
use App\Domain\Section\SectionValidator;
use App\Infrastructure\Persistence\Permission\PermissionRepo;
use Psr\Log\LoggerInterface;

abstract class SectionAction extends Action
{
    protected $sectionRepository;
    protected $permissionRepository;
    protected $sectionValidator;
    protected $sectionPaginator;

    public function __construct(
        LoggerInterface $logger,
        SectionRepository $sectionRepository,
        PermissionRepo $permissionRepo,
        SectionValidator $sectionValidator,
        SectionPaginator $sectionPaginator
    ) {
        parent::__construct($logger);
        $this->sectionRepository = $sectionRepository;
        $this->permissionRepository = $permissionRepo;
        $this->sectionValidator = $sectionValidator;
        $this->sectionPaginator = $sectionPaginator;
    }
}
