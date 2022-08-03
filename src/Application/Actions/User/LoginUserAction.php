<?php

namespace App\Application\Actions\User;

use App\Domain\Permission\Exception\PermissionAuthTokenException;
use App\Domain\Permission\Exception\PermissionLoginException;
use App\Domain\User\Exception\UserPayloadStructureException;
use Psr\Http\Message\ResponseInterface as Response;

class LoginUserAction extends UserAction
{
    /**
     * @throws PermissionAuthTokenException
     * @throws PermissionLoginException
     * @throws UserPayloadStructureException
     */
    protected function action(): Response
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;

        $args = compact('username', 'password');

        $userValidator = UserValidator::getInstance();
        $permissionRepo = $this->permissionRepo;

        $userValidator->checkIfPayloadIsValid($args);

        $permissionRepo->checkIfUserPasswordIsCorrect($username, $password);

        $getAuthToken = $permissionRepo->getAuthToken($username);

        return $this->respondWithData($getAuthToken);
    }
}
