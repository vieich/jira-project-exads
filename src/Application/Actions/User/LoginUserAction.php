<?php

namespace App\Application\Actions\User;

use App\Domain\DomainException\DomainPayloadStructureValidatorException;
use App\Domain\Permission\Exception\PermissionAuthTokenException;
use App\Domain\User\Exception\UserNoAuthorizationException;
use App\Domain\User\Exception\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;

class LoginUserAction extends UserAction
{
    /**
     * @OA\Post(
     *     path="/users/login",
     *     tags= {"Users"},
     *     description="Log in, if success return token",
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
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="ok",
     *          @OA\JsonContent(type = "object",
     *               @OA\Property (property="statusCode", type="integer", example = 401),
     *               @OA\Property (property="error", type="object",
     *                      @OA\Property (property="type", type="string", example = "UNAUTHENTICATED"),
     *                      @OA\Property (property="description", type="string", example = "Password is wrong.")
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
     * @throws DomainPayloadStructureValidatorException
     * @throws PermissionAuthTokenException
     * @throws PermissionAuthTokenException
     * @throws UserNotFoundException
     * @throws UserNoAuthorizationException
     */
    protected function action(): Response
    {
        $data = $this->getFormData();
        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;

        $args = compact('username', 'password');

        $userValidator = $this->userValidator;
        $userRepo = $this->userRepository;

        $userValidator->checkIfPayloadStructureIsValid($args);
        $userRepo->checkIfUserPasswordIsCorrect($username, $password);

        $getAuthToken = $this->permissionRepository->getAuthToken($username);

        $this->logger->info($username . ' successfully log in.');

        return $this->respondWithData($getAuthToken);
    }
}
