<?php

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

class UpdateUserUsernameAction extends UserAction
{
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();
        $userId = (int) $this->resolveArg('id');

        $data = $this->getFormData();
        $username = $data['username'] ?? null;

        $userValidator = $this->userValidator;
        $permissionRepo = $this->permissionRepo;
        $userRepo = $this->userRepository;

        $userValidator->checkIfHeaderIsMissing($auth_token);
        $permissionRepo->checkIfAuthTokenIsValid($auth_token);

        $args = compact('username');
        $userValidator->checkIfPayloadStructureIsValid($args);

        $userByToken = $permissionRepo->getUserByToken($auth_token);
        $userValidator->checkIfUserTokenMatchTheUserId($userId, $userByToken->getId());
        $userValidator->checkIfUsernameIsValid($username);

        $user = $userRepo->updateUserUsername($userId, $username);
        return $this->respondWithData($user);
    }
}
