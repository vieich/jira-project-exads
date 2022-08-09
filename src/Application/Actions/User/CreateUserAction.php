<?php

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

class CreateUserAction extends UserAction
{
    /**
     * @OA\Post(
     *     path="/users/create",
     *     tags= {"Users"},
     *     description="Create user, if success return it",
     *     @OA\RequestBody (
     *          @OA\JsonContent(
     *               type = "object",
     *               @OA\Property (property="username", type="string"),
     *               @OA\Property (property="role", type="string"),
     *               @OA\Property (property="password", type="string"),
     *               @OA\Property (property="confirm_password", type="string")
     *          )
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
     *          )
     *     )
     * )
     */
    protected function action(): Response
    {
        $data = $this->getFormData();
        $username = $data['username'] ?? null;
        $role = $data['role'] ?? null;
        $password = $data['password'] ?? null;
        $confirm_password = $data['confirm_password'] ?? null;

        $args = compact('username', 'role', 'password', 'confirm_password');

        $userValidator = $this->userValidator;

        $userValidator->checkIfPayloadIsValid($args);
        $userValidator->checkIfUsernameIsValid($username);
        $userValidator->checkIfRoleIsValid($role);
        $userValidator->checkIfPasswordFormatIsValid($password);
        $userValidator->checkIfPasswordAndCPasswordMatch($password, $confirm_password);

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $user = $this->userRepository->createUser($username, $role, $hashedPassword);

        return $this->respondWithData($user);
    }
}
