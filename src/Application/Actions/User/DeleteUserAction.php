<?php

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

class DeleteUserAction extends UserAction
{
    /**
     * @OA\Delete(
     *     path="/users/delete/{username}",
     *     tags= {"Users"},
     *     summary="Requires Authentication",
     *     description="Search for an object, if found return it",
     *     @OA\Parameter (
     *          name = "username",
     *          in = "path",
     *          @OA\Schema (type = "string"),
     *          description = "username of the user",
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
     *                      @OA\Property (property="message", type="string"),
     *                      @OA\Property (property="hasSuccess", type="boolean")
     *                      )
     *          )
     *     )
     * )
     */
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();

        $username_to_delete = $this->resolveArg('username');

        $args = compact('username_to_delete');

        $userValidator = $this->userValidator;
        $permissionRepo = $this->permissionRepo;
        $userRepo = $this->userRepository;

        $userValidator->checkIfPayloadIsValid($args);
        $userValidator->checkIfUsernameIsValid($username_to_delete);

        $permissionRepo->checkIfAuthTokenIsValid( $auth_token);
        $permissionRepo->checkIfUserCanDoOperation($auth_token, 'delete');

        $user = $userRepo->deleteUser($username_to_delete);
        return $this->respondWithData($user);
    }
}
