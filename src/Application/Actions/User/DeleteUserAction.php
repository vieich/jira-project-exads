<?php

namespace App\Application\Actions\User;

use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class DeleteUserAction extends UserAction
{

    protected function action(): Response
    {
        $userId = (int) $this->resolveArg('id');

        $data = json_decode(file_get_contents('php://input'), true);
        $userToken = $data['user_token'];

        $user = $this->userRepository->deleteUser($userId, $userToken);

        return $this->respondWithData($user);
    }
}
