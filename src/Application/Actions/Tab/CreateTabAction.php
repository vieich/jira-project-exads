<?php

namespace App\Application\Actions\Tab;

use App\Domain\Permission\Permission;
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
     *               @OA\Property (property="tabName", type="string", example = "Tab"),
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
     *                      @OA\Property (property="description", type="string", example = "Payload is not valid, is missing the tabName field.")
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
     */

    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();

        $data = $this->getFormData();
        $tabName = $data['tabName'] ?? null;
        $ticketId = $data['ticketId'] ?? null;

        $args = compact('tabName', 'ticketId');

        $tabValidator = $this->tabValidator;
        $tabRepo = $this->tabRepository;

        (new Permission($this->permissionRepository))->checkIfHasAccess($auth_token, 'create');

        $tabValidator->checkIfPayloadStructureIsValid($args);
        $tabValidator->checkIfTabNameIsValid($tabName);

        $tab = $tabRepo->createTab($tabName, $ticketId);
        return $this->respondWithData($tab);
    }
}