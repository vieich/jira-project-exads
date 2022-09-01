<?php

namespace App\Application\Actions\Tab;

use App\Domain\DomainException\DomainPayloadStructureValidatorException;
use App\Domain\Permission\Exception\PermissionAuthTokenException;
use App\Domain\Permission\Exception\PermissionNoAuthorizationException;
use App\Domain\Permission\Permission;
use App\Domain\Tab\Exception\TabNameFormatException;
use App\Domain\User\Exception\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use OpenApi\Annotations as OA;


class CreateTabAction extends TabAction
{
    /**
     * @OA\Post(
     *     path="/tabs",
     *     tags= {"Tabs"},
     *     summary="Requires Authentication",
     *     description="Create a Tab, if success return it",
     *     @OA\Parameter (
     *          name = "Auth-Token",
     *          in = "header",
     *          @OA\Schema (type = "string"),
     *          description = "Token for authentication",
     *          required = true,
     *     ),
     *     @OA\RequestBody (
     *          @OA\JsonContent(
     *               type = "object",
     *               @OA\Property (property="name", type="string", example = "Tab"),
     *               @OA\Property (property="ticketId", type="integer", example = 1)
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(type = "object",
     *               @OA\Property (property="statusCode", type="integer", example = 200),
     *               @OA\Property (property="data", type="object",
     *                      @OA\Property (property="id", type="integer", example = 1),
     *                      @OA\Property (property="name", type="string", example = "TicketName"),
     *                      @OA\Property (property="ticketId", type="integer", example = 1),
     *                      @OA\Property (property="isActive", type="boolean", example = true)
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
     *                      @OA\Property (property="description", type="string", example = "Payload is not valid, is missing the name field.")
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
     *     )
     * )
     * @throws DomainPayloadStructureValidatorException
     * @throws TabNameFormatException
     * @throws PermissionNoAuthorizationException
     * @throws PermissionAuthTokenException
     * @throws UserNotFoundException
     */

    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();

        $data = $this->getFormData();
        $name = $data['name'] ?? null;
        $ticketId = $data['ticketId'] ?? null;
        $operation[] = 'create';

        $args = compact('name', 'ticketId');

        $tabValidator = $this->tabValidator;
        $tabRepo = $this->tabRepository;

        (new Permission($this->permissionRepository))->checkIfHasAccess($auth_token, $operation);

        $tabValidator->checkIfPayloadStructureIsValid($args);
        $tabValidator->checkIfTabNameIsValid($name);

        $tab = $tabRepo->createTab($name, $ticketId);

        $this->logger->info('Tab with id ' . $tab->getId() . ' created.');

        return $this->respondWithData($tab);
    }
}