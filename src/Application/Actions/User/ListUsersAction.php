<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

class ListUsersAction extends UserAction
{
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();

        $permissionRepo = $this->permissionRepo;
        $userRepo = $this->userRepository;

        $permissionRepo->checkIfAuthTokenIsValid($auth_token);
        $permissionRepo->checkIfUserCanDoOperation($auth_token, 'read');

        $users = $userRepo->findAll();

        $this->logger->info("Users list was viewed.");

        return $this->respondWithData($users);
    }
}
