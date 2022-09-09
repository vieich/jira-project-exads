<?php

namespace App\Application\Actions\User;

use App\Domain\Permission\Exception\PermissionAuthTokenException;
use App\Domain\Permission\Exception\PermissionNoAuthorizationException;
use App\Domain\Permission\Permission;
use App\Domain\User\Exception\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class DeleteUserAction extends UserAction
{
    /**
     * @OA\Delete(
     *     path="/users/{id}",
     *     tags= {"Users"},
     *     summary="Requires Authentication",
     *     description="Delete a User",
     *     @OA\Parameter (
     *          name = "id",
     *          in = "path",
     *          @OA\Schema (type = "integer"),
     *          description = "id of the User",
     *          required = true,
     *      ),
     *     @OA\Parameter (
     *          name = "Auth-Token",
     *          in = "header",
     *          @OA\Schema (type = "string"),
     *          description = "Token for authentication",
     *          required = true,
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(type = "object",
     *               @OA\Property (property="statusCode", type="integer", example = 200),
     *               @OA\Property (property="data", type="object",
     *                      @OA\Property (property="message", type="string", example = "vitor deleted"),
     *                      @OA\Property (property="hasSuccess", type="boolean", example = true)
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
     *          response="405",
     *          description="ok",
     *          @OA\JsonContent(type = "object",
     *               @OA\Property (property="statusCode", type="integer", example = 403),
     *               @OA\Property (property="error", type="object",
     *                      @OA\Property (property="type", type="string", example = "NOT_ALLOWED"),
     *                      @OA\Property (property="description", type="string", example = "Method not allowed. Must be one of: GET, OPTIONS")
     *                      )
     *          )
     *     ),
     *      @OA\Response(
     *          response="404",
     *          description="ok",
     *          @OA\JsonContent(type = "object",
     *               @OA\Property (property="statusCode", type="integer", example = 404),
     *               @OA\Property (property="error", type="object",
     *                      @OA\Property (property="type", type="string", example = "RESOURCE_NOT_FOUND"),
     *                      @OA\Property (property="description", type="string", example = "User username does not exist.")
     *                      )
     *          )
     *     )
     * )
     * @throws HttpBadRequestException
     * @throws PermissionNoAuthorizationException
     * @throws PermissionAuthTokenException
     * @throws UserNotFoundException
     */
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();
        $operation[] = 'delete';

        $userId = (int) $this->resolveArg('id');

        $userRepo = $this->userRepository;

        (new Permission($this->permissionRepository))->checkIfHasAccess($auth_token, $operation);

        $user = $userRepo->deleteUser($userId);

        $this->logger->info('User with id: ' . $userId . ' deleted.');
        return $this->respondWithData($user);
    }
}
