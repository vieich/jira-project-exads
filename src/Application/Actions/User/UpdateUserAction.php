<?php

namespace App\Application\Actions\User;

use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class UpdateUserAction extends UserAction
{

    protected function action(): Response
    {
        $userId = (int) $this->resolveArg('id');

        $data = json_decode(file_get_contents('php://input'), true);
        $userToken = $data['user_token']  ?? null;
        $userName = $data['user_name'] ?? null;
        $userRole = $data['user_role'] ?? null;
        $userIsActive = $data['user_is_active'] ?? null;


        $user = $this->userRepository->updateUser($userId, $userToken, $userName, $userRole, $userIsActive);

        return $this->respondWithData($user);
    }
}