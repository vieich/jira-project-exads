<?php

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

class UpdateUserAction extends UserAction
{
    protected function action(): Response
    {
        $userId = (int) $this->resolveArg('id');

        $data = $this->getFormData();
        $username = $data['name'] ?? null;
        $userRole = $data['role'] ?? null;
        $userIsActive = $data['is_active'] ?? null;
        $userToken = $data['password']  ?? null;

        if(isset($username)) {
            if (!UserValidator::getInstance()->checkIfUsernameIsValid($username)) {
                throw new UserPayloadDataException('Username not valid');
            }
        }

        if(isset($userRole)) {
            if (!UserValidator::getInstance()->isRoleValid($userRole)) {
                throw new UserPayloadDataException('Role not valid');
            }
        }

        $user = $this->userRepository->updateUser($userId, $userToken, $username, $userRole, $userIsActive);

        return $this->respondWithData($user);
    }
}