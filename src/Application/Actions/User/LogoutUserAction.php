<?php

namespace App\Application\Actions\User;

use App\Domain\User\Exception\UserPayloadStructureException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class LogoutUserAction extends UserAction
{

    protected function action(): Response
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $userName = $data['username'] ?? null;

        if (!isset($userName)) {
            throw new UserPayloadStructureException('Payload does not meet the requirements');
        }

        $result = $this->userRepository->updateIsActive($userName, 'logout');

        return $this->respondWithData($result);
    }
}