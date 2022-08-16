<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Application\Actions\Action;
use App\Domain\User\UserRepository;
use App\Domain\User\UserValidator;
use App\Infrastructure\Persistence\Permission\PermissionRepo;
use Psr\Log\LoggerInterface;

abstract class UserAction extends Action
{
    protected $userRepository;
    protected $permissionRepository;
    protected $userValidator;

    public function __construct(LoggerInterface $logger, UserRepository $userRepository, PermissionRepo $permissionRepo, UserValidator $userValidator)
    {
        parent::__construct($logger);
        $this->userRepository = $userRepository;
        $this->permissionRepository = $permissionRepo;
        $this->userValidator = $userValidator;
    }
}
