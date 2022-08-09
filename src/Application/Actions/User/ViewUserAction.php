<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;
use OpenApi\Annotations as OA;

class ViewUserAction extends UserAction
{
    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     tags= {"Users"},
     *     summary="Requires Authentication",
     *     description="Search for an object, if found return it",
     *     @OA\Parameter (
     *          name = "id",
     *          in = "path",
     *          @OA\Schema (type = "integer"),
     *          description = "Id of the user",
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
     *               @OA\Property (property="statusCode", type="integer"),
     *               @OA\Property (property="data", type="object",
     *                      @OA\Property (property="id", type="integer"),
     *                      @OA\Property (property="username", type="string"),
     *                      @OA\Property (property="role", type="string"),
     *                      @OA\Property (property="isActive", type="boolean")
     *                      )
     *              )
     *     )
     * )
     */
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();
        $userId = (int) $this->resolveArg('id');

        $permissionRepo = $this->permissionRepo;
        $userRepo = $this->userRepository;

        $permissionRepo->checkIfAuthTokenIsValid($auth_token);
        $permissionRepo->checkIfUserCanDoOperation($auth_token, 'read');

        $user = $userRepo->findUserOfId($userId);

        $this->logger->info("User of id `${userId}` was viewed.");

        return $this->respondWithData($user);
    }
}
