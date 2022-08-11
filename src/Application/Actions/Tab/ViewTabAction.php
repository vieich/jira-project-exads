<?php

namespace App\Application\Actions\Tab;

use Psr\Http\Message\ResponseInterface as Response;

class ViewTabAction extends TabAction
{
    /**
     * @OA\Get(
     *     path="/tabs/{id}",
     *     tags= {"Tabs"},
     *     summary="Requires Authentication",
     *     description="Search for specific Tab by id, if found return it",
     *     @OA\Parameter (
     *          name = "Auth-Token",
     *          in = "header",
     *          @OA\Schema (type = "string"),
     *          description = "Token for authentication",
     *          required = true,
     *     ),
     *     @OA\Parameter (
     *          name = "id",
     *          in = "path",
     *          @OA\Schema (type = "integer"),
     *          description = "Id of the ticket",
     *          required = true,
     *      ),
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
     *     @OA\Response(
     *          response="404",
     *          description="ok",
     *          @OA\JsonContent(type = "object",
     *               @OA\Property (property="statusCode", type="integer", example = 404),
     *               @OA\Property (property="error", type="object",
     *                      @OA\Property (property="type", type="string", example = "RESOURCE_NOT_FOUND"),
     *                      @OA\Property (property="description", type="string", example = "The Tab does not exist.")
     *                      )
     *          )
     *     )
     * )
     */
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();
        $tabId = (int) $this->resolveArg('id');

        $permissionRepo = $this->permissionRepository;
        $tabRepo = $this->tabRepository;
        $tabValidator = $this->tabValidator;

        $tabValidator->checkIfHeaderIsMissing($auth_token);
        $permissionRepo->checkIfAuthTokenIsValid($auth_token);
        $permissionRepo->checkIfUserCanDoOperation($auth_token, 'read');

        $tab = $tabRepo->findTabById($tabId);
        return $this->respondWithData($tab);
    }
}