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
     * @OA\Patch(
     *     path="/users/{id}",
     *     tags= {"Users"},
     *     summary="Requires Authentication",
     *     description="Update a User username, if success return it",
     *     @OA\Parameter (
     *          name = "Auth-Token",
     *          in = "header",
     *          @OA\Schema (type = "string"),
     *          description = "Token for authentication",
     *          required = true,
     *     ),
     *     @OA\Parameter (
     *          name = "id",
     *          in = "path",
     *          @OA\Schema (type = "integer"),
     *          description = "Id of the User",
     *          required = true,
     *      ),
     *     @OA\RequestBody (
     *          @OA\JsonContent(
     *               type = "object",
     *               @OA\Property (property="username", type="string"),
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(type = "object",
     *               @OA\Property (property="statusCode", type="integer", example = 200),
     *               @OA\Property (property="data", type="object",
     *                      @OA\Property (property="id", type="integer", example = 1),
     *                      @OA\Property (property="username", type="string", example = "Vitor"),
     *                      @OA\Property (property="role", type="string", example = "client"),
     *                      @OA\Property (property="isActive", type="boolean", example = true),
     *                      )
     *              )
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="ok",
     *          @OA\JsonContent(type = "object",
     *               @OA\Property (property="statusCode", type="integer", example = 400),
     *               @OA\Property (property="error", type="object",
     *                      @OA\Property (property="type", type="string", example = "BAD_REQUEST"),
     *                      @OA\Property (property="description", type="string", example = "Payload is not valid, is missing the oldPassword field.")
     *                      )
     *          )
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="ok",
     *          @OA\JsonContent(type = "object",
     *               @OA\Property (property="statusCode", type="integer", example = 401),
     *               @OA\Property (property="error", type="object",
     *                      @OA\Property (property="type", type="string", example = "UNAUTHENTICATED"),
     *                      @OA\Property (property="description", type="string", example = "Log in to get an valid auth token.")
     *                      )
     *          )
     *     ),
     *      @OA\Response(
     *          response="403",
     *          description="ok",
     *          @OA\JsonContent(type = "object",
     *               @OA\Property (property="statusCode", type="integer", example = 403),
     *               @OA\Property (property="error", type="object",
     *                      @OA\Property (property="type", type="string", example = "INSUFFICIENT_PRIVILEGES"),
     *                      @OA\Property (property="description", type="string", example = "Auth-Token is missing on the header.")
     *                      )
     *          )
     *     ),
     *      @OA\Response(
     *          response="404",
     *          description="ok",
     *          @OA\JsonContent(type = "object",
     *               @OA\Property (property="statusCode", type="integer", example = 401),
     *               @OA\Property (property="error", type="object",
     *                      @OA\Property (property="type", type="string", example = "UNAUTHORIZED"),
     *                      @OA\Property (property="description", type="string", example = "The user that you are trying to update is not yours.")
     *                      )
     *          )
     *     )
     * )
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
