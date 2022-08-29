<?php

namespace App\Application\Actions\User;

use App\Domain\DomainException\DomainPayloadStructureValidatorException;
use App\Domain\User\Exception\UserPasswordFormatException;
use App\Domain\User\Exception\UserRoleException;
use App\Domain\User\Exception\UserUsernameFormatException;
use Psr\Http\Message\ResponseInterface as Response;

class CreateUserAction extends UserAction
{
    /**
     * @OA\Post(
     *     path="/users",
     *     tags= {"Users"},
     *     description="Create user, if success return it",
     *     @OA\RequestBody (
     *          @OA\JsonContent(
     *               type = "object",
     *               @OA\Property (property="username", type="string"),
     *               @OA\Property (property="role", type="string"),
     *               @OA\Property (property="password", type="string"),
     *               @OA\Property (property="confirmPassword", type="string")
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(
     *               required={"statusCode","data"},
     *               type = "object",
     *               @OA\Property (property="statusCode", type="integer"),
     *               @OA\Property (property="data", type="object",
     *                      required={"id","username","role","isActive"},
     *                      @OA\Property (property="id", type="integer"),
     *                      @OA\Property (property="username", type="string"),
     *                      @OA\Property (property="role", type="string"),
     *                      @OA\Property (property="isActive", type="boolean")
     *                      )
     *          )
     *     )
     * )
     * @throws DomainPayloadStructureValidatorException
     * @throws UserUsernameFormatException
     * @throws UserRoleException
     * @throws UserPasswordFormatException
     */
    protected function action(): Response
    {
        $data = $this->getFormData();
        $username = $data['username'] ?? null;
        $role = $data['role'] ?? null;
        $password = $data['password'] ?? null;
        $confirmPassword = $data['confirmPassword'] ?? null;

        $args = compact('username', 'role', 'password', 'confirmPassword');

        $userValidator = $this->userValidator;

        $userValidator->checkIfPayloadStructureIsValid($args);
        $userValidator->checkIfUsernameIsValid($username);
        $userValidator->checkIfRoleIsValid($role);
        $userValidator->checkIfPasswordFormatIsValid($password);
        $userValidator->checkIfPasswordAndCPasswordMatch($password, $confirmPassword);

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $user = $this->userRepository->createUser($username, $role, $hashedPassword);

        $this->logger->info('User with id ' . $user->getId() . ' created');

        return $this->respondWithData($user);
    }
}
