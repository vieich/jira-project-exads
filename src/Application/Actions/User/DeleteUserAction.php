<?php

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

class DeleteUserAction extends UserAction
{

    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();

        $data = $this->getFormData();
        $username_to_delete = $data['username_to_delete'] ?? null;

        $args = compact('username_to_delete');

        $userValidator = UserValidator::getInstance();
        $permissionRepo = $this->permissionRepo;
        $userRepo = $this->userRepository;

        $userValidator->checkIfPayloadIsValid($args);
        $userValidator->checkIfUsernameIsValid($username_to_delete);

        $permissionRepo->checkIfAuthTokenIsValid( $auth_token);
        $permissionRepo->checkIfUserCanDoOperation($auth_token, 'delete');

        $user = $userRepo->deleteUser($username_to_delete);
        return $this->respondWithData($user);
    }
}
