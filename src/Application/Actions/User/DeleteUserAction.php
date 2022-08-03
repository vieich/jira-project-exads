<?php

namespace App\Application\Actions\User;

use App\Application\Actions\Permission\PermissionValidator;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteUserAction extends UserAction
{

    protected function action(): Response
    {
        $headers = apache_request_headers();
        $auth_token = $headers['Auth-Token'] ?? null;

        $data = json_decode(file_get_contents('php://input'), true);
        $username_to_delete = $data['username_to_delete'] ?? null;

        $args = compact('username_to_delete');

        $userValidator = UserValidator::getInstance();
        $permissionValidator = PermissionValidator::getInstance();
        $permissionRepo = $this->permissionRepo;
        $userRepo = $this->userRepository;

        $userValidator->checkIfPayloadIsValid($args);
        $userValidator->checkIfUsernameIsValid($username_to_delete);

        $permissionValidator->checkIfAuthTokenFormatIsValid($auth_token);
        $permissionRepo->checkIfAuthTokenIsValid( $auth_token);
        $permissionRepo->checkIfUserCanDoOperation($auth_token, 'delete');

        $user = $userRepo->deleteUser($username_to_delete);
        return $this->respondWithData($user);
    }
}
