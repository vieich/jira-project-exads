<?php

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

class LoginUserAction extends UserAction
{
    /**
     * @OA\Post(
     *     path="/users/login",
     *     tags= {"Users"},
     *     description="Create user, if success return it",
     *     @OA\RequestBody (
     *          @OA\JsonContent(
     *               type = "object",
     *               @OA\Property (property="username", type="string"),
     *               @OA\Property (property="password", type="string"),
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(type = "object",
     *               @OA\Property (property="statusCode", type="integer"),
     *               @OA\Property (property="data", type="object",
     *                      @OA\Property (property="token", type="string"),
     *                      @OA\Property (property="hasSuccess", type="boolean")
     *                      )
     *          )
     *     )
     * )
     */
    protected function action(): Response
    {
        $data = $this->getFormData();
        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;

        $args = compact('username', 'password');

        $userValidator = $this->userValidator;
        $permissionRepo = $this->permissionRepo;
        $userRepo = $this->userRepository;

        $userValidator->checkIfPayloadIsValid($args);
        $userRepo->checkIfUserExists($username);
        $userRepo->checkIfUserPasswordIsCorrect($username, $password);

        $getAuthToken = $permissionRepo->getAuthToken($username);

        return $this->respondWithData($getAuthToken);
    }
}
