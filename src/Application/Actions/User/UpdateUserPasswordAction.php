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
     * @OA\Patch(
     *     path="/users/password/{id}",
     *     tags= {"Users"},
     *     summary="Requires Authentication",
     *     description="Update a User password",
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
     *               @OA\Property (property="oldPassword", type="string"),
     *               @OA\Property (property="newPassword", type="string"),
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(type = "object",
     *               @OA\Property (property="statusCode", type="integer", example = 200),
     *               @OA\Property (property="data", type="object",
     *                      @OA\Property (property="message", type="string", example = "Vitor password was successfully updated"),
     *                      @OA\Property (property="hasSuccess", type="boolean", example = true),
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

        $this->logger->info($userByToken->getName() . ' successfully changed password.');

        return $this->respondWithData($action);
    }
}
