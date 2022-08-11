<?php

namespace App\Application\Actions\Ticket;

use Psr\Http\Message\ResponseInterface as Response;

class DeleteTicketAction extends TicketAction
{
    /**
     * @OA\Delete (
     *     path="/tickets/{id}",
     *     tags= {"Tickets"},
     *     summary="Requires Authentication",
     *     description="Delete a ticket, if success return it",
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
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(type = "object",
     *               @OA\Property (property="statusCode", type="integer", example = 200),
     *               @OA\Property (property="data", type="object",
     *                      @OA\Property (property="message", type="string", example = "Ticket with id 5 deleted"),
     *                      @OA\Property (property="hasSuccess", type="boolean", example = true)
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

        $ticketValidator = $this->ticketValidator;
        $permissionRepo = $this->permissionRepo;
        $ticketRepo = $this->ticketRepository;

        $ticketValidator->checkIfHeaderIsMissing($auth_token);
        $permissionRepo->checkIfAuthTokenIsValid($auth_token);
        $permissionRepo->checkIfUserCanDoOperation($auth_token, 'delete');

        $action = $ticketRepo->deleteTicket($ticketId);
        return $this->respondWithData($action);
    }
}
