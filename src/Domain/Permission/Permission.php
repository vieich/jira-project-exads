<?php

namespace App\Domain\Permission;

use App\Domain\Permission\Exception\PermissionAuthTokenException;
use App\Domain\Permission\Exception\PermissionNoAuthorizationException;
use App\Domain\User\Exception\UserNoAuthorizationException;
use App\Domain\User\Exception\UserNotFoundException;
use App\Infrastructure\Persistence\Permission\PermissionRepo;

class Permission
{
    private $permissionRepo;

    /**
     * @param PermissionRepo $permissionRepo
     */
    public function __construct(PermissionRepo $permissionRepo)
    {
        $this->permissionRepo = $permissionRepo;
    }

    /**
     * @param string $auth_token
     * @param array $operation
     *
     * @throws PermissionAuthTokenException
     * @throws PermissionNoAuthorizationException
     * @throws UserNotFoundException
     */
    public function checkIfHasAccess(string $auth_token, array $operation)
    {
        if ($auth_token == "") {
            throw new PermissionNoAuthorizationException('Auth-Token is missing on the header.');
        }

        $this->permissionRepo->checkIfAuthTokenIsValid($auth_token);
        $this->permissionRepo->checkIfUserCanDoOperation($auth_token, $operation);
    }
}
