<?php

namespace App\Application\Actions\Ticket;

use Psr\Http\Message\ResponseInterface as Response;

class UpdateTicketAction extends TicketAction
{
    /**
     * @OA\Patch(
     *     path="/tickets/{id}",
     *     tags= {"Tickets"},
     *     summary="Requires Authentication",
     *     description="Update a ticket, if success return it",
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
     *     @OA\RequestBody (
     *          @OA\JsonContent(
     *               type = "object",
     *               @OA\Property (property="ticketName", type="string"),
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
     *                      @OA\Property (property="userId", type="integer", example = 1),
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
     *                      @OA\Property (property="description", type="string", example = "Payload is not valid, is missing the ticketName field.")
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
     *     ),
     *      @OA\Response(
     *          response="404",
     *          description="ok",
     *          @OA\JsonContent(type = "object",
     *               @OA\Property (property="statusCode", type="integer", example = 404),
     *               @OA\Property (property="error", type="object",
     *                      @OA\Property (property="type", type="string", example = "RESOURCE_NOT_FOUND"),
     *                      @OA\Property (property="description", type="string", example = "The ticket does not exist.")
     *                      )
     *          )
     *     )
     * )
     */
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();
        $ticketId = (int) $this->resolveArg('id');

        $data = $this->getFormData();
        $ticketName = $data['ticketName'] ?? null;

        $valuesToUpdate = compact('ticketName');

        $ticketValidator = $this->ticketValidator;
        $ticketRepo = $this->ticketRepository;
        $permissionRepo = $this->permissionRepo;

        $ticketValidator->checkIfHeaderIsMissing($auth_token);
        $permissionRepo->checkIfAuthTokenIsValid($auth_token);
        $permissionRepo->checkIfUserCanDoOperation($auth_token, 'update');

        $ticketValidator->checkIfPayloadStructureIsValid($valuesToUpdate);
        $ticketValidator->checkIfTicketNameIsValid($ticketName);

        $ticket = $ticketRepo->updateTicket($ticketId, $ticketName);
        return $this->respondWithData($ticket);
    }
}
