<?php

namespace App\Application\Actions\User;

use App\Domain\User\Exception\UserPayloadDataException;
use App\Domain\User\Exception\UserPayloadStructureException;
use Psr\Http\Message\ResponseInterface as Response;

class CreateUserAction extends UserAction
{

    protected function action(): Response
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $username = $data['username'];
        $userRole = $data['role'];
        $userPassword = $data['password'];
        $userConfirmPassword = $data['confirm_password'];

        if (!isset($username) || !isset($userRole) || !isset($userPassword) || !isset($userConfirmPassword)) {
            throw new UserPayloadStructureException('Payload does not meet the requirements');
        }

        if (!UserValidator::getInstance()->isUsernameValid($username)) {
            throw new UserPayloadDataException('Username not valid');
        }

        if (!UserValidator::getInstance()->isRoleValid($userRole)) {
            throw new UserPayloadDataException('Role not valid');
        }

        if (!UserValidator::getInstance()->isPasswordValid($userPassword)) {
            throw new UserPayloadDataException('Password not valid');
        }

        if (!UserValidator::getInstance()->doesPasswordMatch($userPassword, $userConfirmPassword)) {
            throw new UserPayloadDataException('Field confirm password does not match with password');
        }

        $hashedPassword = password_hash($userPassword, PASSWORD_DEFAULT);

        $user = $this->userRepository->createUser($username, $userRole, $hashedPassword);

        return $this->respondWithData($user);
    }
}
