<?php

namespace App\Application\Actions\User;

use App\Domain\DomainException\DomainPayloadStructureValidatorException;
use App\Domain\Permission\Exception\PermissionAuthTokenException;
use App\Domain\Permission\Exception\PermissionNoAuthorizationException;
use App\Domain\Permission\Permission;
use App\Domain\User\Exception\UserNoAuthorizationException;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\Exception\UserPasswordFormatException;
use App\Domain\User\Exception\UserPayloadDataException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class UpdateUserPasswordAction extends UserAction
{

    /**
     * @throws UserNotFoundException
     * @throws PermissionAuthTokenException
     * @throws UserNoAuthorizationException
     * @throws UserPasswordFormatException
     * @throws UserPayloadDataException
     * @throws PermissionNoAuthorizationException
     * @throws HttpBadRequestException
     * @throws DomainPayloadStructureValidatorException
     */
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();
        $userId = (int) $this->resolveArg('id');

        $data = $this->getFormData();
        $oldPassword = $data['oldPassword'] ?? null;
        $newPassword = $data['newPassword'] ?? null;
        $operation[] = 'updateUser';

        $userValidator = $this->userValidator;
        $permissionRepo = $this->permissionRepository;
        $userRepo = $this->userRepository;

        (new Permission($this->permissionRepository))->checkIfHasAccess($auth_token, $operation);

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
