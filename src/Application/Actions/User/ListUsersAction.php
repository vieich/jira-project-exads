<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

class ListUsersAction extends UserAction
{
    /**
     * @OA\Get(
     *     path="/users",
     *     tags= {"Users"},
     *     summary="Requires Authentication",
     *     description="Search for an object, if found return it",
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
     *               @OA\Property (property="data", type="array",
     *                      @OA\Items(
     *                      @OA\Property (property="id", type="integer"),
     *                      @OA\Property (property="username", type="string"),
     *                      @OA\Property (property="role", type="string"),
     *                      @OA\Property (property="isActive", type="boolean")
     *                          )
     *                  )
     *              )
     *     )
     * )
     */
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();

        $permissionRepo = $this->permissionRepo;
        $userRepo = $this->userRepository;

        $permissionRepo->checkIfAuthTokenIsValid($auth_token);
        $permissionRepo->checkIfUserCanDoOperation($auth_token, 'read');

        $users = $userRepo->findAll();

        $this->logger->info("Users list was viewed.");

        return $this->respondWithData($users);
    }
}
