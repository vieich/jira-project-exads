<?php

namespace App\Application\Actions\User;

use App\Domain\User\Exception\UserPayloadDataException;
use Psr\Http\Message\ResponseInterface as Response;

class UpdateUserPasswordAction extends UserAction
{

    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();
        $userId = (int) $this->resolveArg('id');

        $data = $this->getFormData();
        $oldPassword = $data['oldPassword'] ?? null;
        $newPassword = $data['newPassword'] ?? null;

        $userValidator = $this->userValidator;
        $permissionRepo = $this->permissionRepo;
        $userRepo = $this->userRepository;

        $userValidator->checkIfHeaderIsMissing($auth_token);
        $permissionRepo->checkIfAuthTokenIsValid($auth_token);
        $userByToken = $permissionRepo->getUserByToken($auth_token);
        $userValidator->checkIfUserTokenMatchTheUserId($userId, $userByToken->getId());

        $args = compact('oldPassword', 'newPassword');
        $userValidator->checkIfPayloadStructureIsValid($args);

        if ($oldPassword == $newPassword) {
            throw new UserPayloadDataException('newPassword must be different than the oldPassword');
        }

        $userValidator->checkIfPasswordFormatIsValid($newPassword);

        $action = $userRepo->updateUserPassword($userId, $oldPassword, $newPassword);
        return $this->respondWithData($action);
    }
}
