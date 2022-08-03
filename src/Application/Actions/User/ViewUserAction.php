<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Application\Actions\Permission\PermissionValidator;
use Psr\Http\Message\ResponseInterface as Response;

class ViewUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $headers = apache_request_headers();
        $auth_token = $headers['Auth-Token'] ?? null;

        $userId = (int) $this->resolveArg('id');

        $permissionValidator = PermissionValidator::getInstance();

        $permissionRepo = $this->permissionRepo;
        $userRepo = $this->userRepository;

        $permissionValidator->checkIfAuthTokenFormatIsValid($auth_token);
        $permissionRepo->checkIfAuthTokenIsValid($auth_token);

        $permissionRepo->checkIfUserCanDoOperation($auth_token, 'read');

        $user = $userRepo->findUserOfId($userId);

        $this->logger->info("User of id `${userId}` was viewed.");

        return $this->respondWithData($user);
    }
}
