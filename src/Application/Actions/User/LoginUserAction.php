<?php

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

class LoginUserAction extends UserAction
{
    protected function action(): Response
    {
        $data = $this->getFormData();
        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;

        $args = compact('username', 'password');

        $userValidator = UserValidator::getInstance();
        $permissionRepo = $this->permissionRepo;
        $userRepo = $this->userRepository;

        $userValidator->checkIfPayloadIsValid($args);
        $userRepo->checkIfUserExists($username);
        $userRepo->checkIfUserPasswordIsCorrect($username, $password);

        $getAuthToken = $permissionRepo->getAuthToken($username);

        return $this->respondWithData($getAuthToken);
    }
}
