<?php

namespace App\Application\Actions\User;

use App\Domain\User\Exception\UserPayloadDataException;
use App\Domain\User\Exception\UserPayloadStructureException;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteUserAction extends UserAction
{

    protected function action(): Response
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $username = $data['username'] ?? null;

        if (!isset($username)) {
            throw new UserPayloadStructureException('Payload does not meet the requirements');
        }

        if(!UserValidator::getInstance()->isUsernameValid($username)) {
            throw new UserPayloadDataException('Username is not valid');
        }

        $user = $this->userRepository->deleteUser($username);

        return $this->respondWithData($user);
    }
}
