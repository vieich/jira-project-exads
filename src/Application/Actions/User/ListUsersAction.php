<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Application\Actions\Permission\PermissionValidator;
use Psr\Http\Message\ResponseInterface as Response;

class ListUsersAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $headers = apache_request_headers();
        $auth_token = $headers['Auth-Token'] ?? null;

        $permissionValidator = PermissionValidator::getInstance();

        $permissionRepo = $this->permissionRepo;
        $userRepo = $this->userRepository;

        $permissionValidator->checkIfAuthTokenFormatIsValid($auth_token);

        $permissionRepo->checkIfAuthTokenIsValid($auth_token);
        $permissionRepo->checkIfUserCanDoOperation($auth_token, 'read');

        $users = $userRepo->findAll();

        $this->logger->info("Users list was viewed.");

        return $this->respondWithData($users);
    }
}
