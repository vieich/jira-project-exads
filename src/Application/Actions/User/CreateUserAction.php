<?php

namespace App\Application\Actions\User;

use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class CreateUserAction extends UserAction
{

    protected function action(): Response
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $userName = $data['user_name'];
        $userRole = $data['user_role'];
        $userIsActive = $data['user_is_active'];

        $user = $this->userRepository->createUser($userName, $userRole, $userIsActive);

        return $this->respondWithData($user);
    }
}