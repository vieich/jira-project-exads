<?php

namespace App\Application\Actions\Ticket;

use Psr\Http\Message\ResponseInterface as Response;

class DeleteTicketAction extends TicketAction
{
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();
        $ticketId = (int) $this->resolveArg('id');

        $permissionRepo = $this->permissionRepo;
        $ticketRepo = $this->ticketRepository;

        $permissionRepo->checkIfAuthTokenIsValid($auth_token);
        $permissionRepo->checkIfUserCanDoOperation($auth_token, 'delete');

        $action = $ticketRepo->deleteTicket($ticketId);
        return $this->respondWithData($action);
    }
}
