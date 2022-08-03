<?php

namespace App\Application\Actions\User;

use App\Application\Actions\Permission\PermissionValidator;
use Psr\Http\Message\ResponseInterface as Response;

class CreateUserAction extends UserAction
{
    protected function action(): Response
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $username = $data['username'] ?? null;
        $role = $data['role'] ?? null;
        $password = $data['password'] ?? null;
        $confirm_password = $data['confirm_password'] ?? null;

        $args = compact('username', 'role', 'password', 'confirm_password');

        $userValidator = UserValidator::getInstance();
        $permissionValidator = PermissionValidator::getInstance();

        $userValidator->checkIfPayloadIsValid($args);
        $userValidator->checkIfUsernameIsValid($username);
        $permissionValidator->checkIfRoleIsValid($role);
        $permissionValidator->checkIfPasswordFormatIsValid($password);
        $permissionValidator->checkIfPasswordAndCpassordMatch($password, $confirm_password);

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $user = $this->userRepository->createUser($username, $role, $hashedPassword);

        return $this->respondWithData($user);
    }
}
