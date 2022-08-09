<?php

namespace App\Application\Actions\Ticket;

use Psr\Http\Message\ResponseInterface as Response;

class CreateTicketAction extends TicketAction
{
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();

        $data = $this->getFormData();
        $ticket_name = $data['ticket_name'] ?? null;

        $args = compact('ticket_name');

        $ticketValidator = $this->ticketValidator;
        $ticketRepo = $this->ticketRepository;
        $permissionRepo = $this->permissionRepo;

        $ticketValidator->checkIfHeaderIsMissing($auth_token);
        $permissionRepo->checkIfAuthTokenIsValid($auth_token);
        $permissionRepo->checkIfUserCanDoOperation($auth_token, 'create');

        $ticketValidator->checkIfPayloadFormatIsValid($args);
        $ticketValidator->checkIfTicketNameIsValid($ticket_name);

        $user = $permissionRepo->getUserByToken($auth_token);

        $ticket = $ticketRepo->createTicket($ticket_name, $user->getId());
        return $this->respondWithData($ticket);
    }
}
