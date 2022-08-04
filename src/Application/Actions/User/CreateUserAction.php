<?php

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

class CreateUserAction extends UserAction
{
    protected function action(): Response
    {
        $data = $this->getFormData();
        $username = $data['username'] ?? null;
        $role = $data['role'] ?? null;
        $password = $data['password'] ?? null;
        $confirm_password = $data['confirm_password'] ?? null;

        $args = compact('username', 'role', 'password', 'confirm_password');

        $userValidator = UserValidator::getInstance();

        $userValidator->checkIfPayloadIsValid($args);
        $userValidator->checkIfUsernameIsValid($username);
        $userValidator->checkIfRoleIsValid($role);
        $userValidator->checkIfPasswordFormatIsValid($password);
        $userValidator->checkIfPasswordAndCPasswordMatch($password, $confirm_password);

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $user = $this->userRepository->createUser($username, $role, $hashedPassword);

        return $this->respondWithData($user);
    }
}
