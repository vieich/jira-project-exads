<?php

namespace App\Application\Actions\User;

use App\Domain\User\Exception\UserPayloadStructureException;
use App\Domain\User\UserNoAuthorizationException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class LoginUserAction extends UserAction
{
    protected function action(): Response
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $userName = $data['username'] ?? null;
        $userPassword = $data['password'] ?? null;

        if (!isset($userName)  || !isset($userPassword)) {
            throw new UserPayloadStructureException('Payload does not meet the requirements');
        }

        if (!$this->permissionRepo->checkIfUserPasswordIsCorrect($userName, $userPassword)) {
            throw new UserNoAuthorizationException('Login failed, wrong password or username');
        }

        $result = $this->userRepository->updateIsActive($userName, 'login');

        return $this->respondWithData($result);
    }
}
