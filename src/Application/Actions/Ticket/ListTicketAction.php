<?php

namespace App\Application\Actions\Ticket;

use Psr\Http\Message\ResponseInterface as Response;

class ListTicketAction extends TicketAction
{

    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();

        $permissionRepo = $this->permissionRepo;
        $ticketValidator = $this->ticketValidator;

        $ticketValidator->checkIfHeaderIsMissing($auth_token);

        $permissionRepo->checkIfAuthTokenIsValid($auth_token);
        $permissionRepo->checkIfUserCanDoOperation($auth_token,'read');

        $tickets = $this->ticketRepository->findAll();

        return $this->respondWithData($tickets);
    }
}
