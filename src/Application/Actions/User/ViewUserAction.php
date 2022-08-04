<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Application\Actions\Permission\PermissionValidator;
use Psr\Http\Message\ResponseInterface as Response;

class ViewUserAction extends UserAction
{
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();
        $userId = (int) $this->resolveArg('id');

        $permissionRepo = $this->permissionRepo;
        $userRepo = $this->userRepository;

        $permissionRepo->checkIfAuthTokenIsValid($auth_token);
        $permissionRepo->checkIfUserCanDoOperation($auth_token, 'read');

        $user = $userRepo->findUserOfId($userId);

        $this->logger->info("User of id `${userId}` was viewed.");

        return $this->respondWithData($user);
    }
}
