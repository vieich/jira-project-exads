<?php

namespace App\Application\Actions\Ticket;

use App\Application\Actions\Permission\PermissionValidator;
use App\Domain\Ticket\Exception\TicketPayloadDataException;
use App\Domain\Ticket\Exception\TicketPayloadStructureException;
use App\Domain\User\Exception\UserNoAuthorizationException;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteTicketAction extends TicketAction
{
    protected function action(): Response
    {
        $headers = apache_request_headers();
        $auth_token = $headers['Auth-Token'] ?? null;

        $ticketId = (int) $this->resolveArg('id');

        $permissionValidator = PermissionValidator::getInstance();

        $permissionRepo = $this->permissionRepo;

        $permissionValidator->checkIfAuthTokenFormatIsValid($auth_token);
        $permissionRepo->checkIfAuthTokenIsValid($auth_token);

        $permissionRepo->checkIfUserCanDoOperation($auth_token, 'delete');


        $action = $this->ticketRepository->deleteTicket($ticketId);
        return $this->respondWithData($action);
    }
}
