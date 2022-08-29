<?php

namespace App\Application\Actions\User;

use App\Domain\DomainException\DomainPayloadStructureValidatorException;
use App\Domain\Permission\Exception\PermissionAuthTokenException;
use App\Domain\Permission\Exception\PermissionNoAuthorizationException;
use App\Domain\Permission\Permission;
use App\Domain\User\Exception\UserNoAuthorizationException;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\Exception\UserUsernameFormatException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class UpdateUserUsernameAction extends UserAction
{
    /**
     * @throws UserNotFoundException
     * @throws PermissionAuthTokenException
     * @throws UserNoAuthorizationException
     * @throws UserUsernameFormatException
     * @throws PermissionNoAuthorizationException
     * @throws HttpBadRequestException
     * @throws DomainPayloadStructureValidatorException
     */
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();
        $userId = (int) $this->resolveArg('id');

        $data = $this->getFormData();
        $username = $data['username'] ?? null;
        $operation[] = 'updateUser';
        $args = compact('username');

        $userValidator = $this->userValidator;
        $permissionRepo = $this->permissionRepository;
        $userRepo = $this->userRepository;

        (new Permission($this->permissionRepository))->checkIfHasAccess($auth_token, $operation);

        $userValidator->checkIfPayloadStructureIsValid($args);

        $userByToken = $permissionRepo->getUserByToken($auth_token);
        $userValidator->checkIfUserTokenMatchTheUserId($userId, $userByToken->getId());
        $userValidator->checkIfUsernameIsValid($username);

        $user = $userRepo->updateUserUsername($userId, $username);

        $this->logger->info('User successfully changed name to ' . $username);

        return $this->respondWithData($user);
    }
}
