<?php

namespace App\Application\Actions\Ticket;

use Psr\Http\Message\ResponseInterface as Response;
use OpenApi\Annotations as OA;

class ViewTicketAction extends TicketAction
{
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();
        $ticketId = (int) $this->resolveArg('id');

        $permissionRepo = $this->permissionRepo;
        $ticketRepo = $this->ticketRepository;
        $ticketValidator = $this->ticketValidator;

        $ticketValidator->checkIfHeaderIsMissing($auth_token);

        $permissionRepo->checkIfAuthTokenIsValid($auth_token);
        $permissionRepo->checkIfUserCanDoOperation($auth_token,'read');

        $ticket = $ticketRepo->findTicketById($ticketId);
        return $this->respondWithData($ticket);
    }
}