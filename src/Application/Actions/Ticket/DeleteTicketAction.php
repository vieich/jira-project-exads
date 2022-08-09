<?php

namespace App\Application\Actions\Ticket;

use Psr\Http\Message\ResponseInterface as Response;

class DeleteTicketAction extends TicketAction
{
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
