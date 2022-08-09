<?php

namespace App\Application\Actions\Ticket;

use Psr\Http\Message\ResponseInterface as Response;

class UpdateTicketAction extends TicketAction
{
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();
        $ticketId = (int) $this->resolveArg('id');

        $data = $this->getFormData();
        $name = $data['name'] ?? null;
        //$is_done = $data['is_done'] ?? null;

        $valuesToUpdate = compact('name');

        $ticketValidator = $this->ticketValidator;
        $ticketRepo = $this->ticketRepository;
        $permissionRepo = $this->permissionRepo;

        $permissionRepo->checkIfAuthTokenIsValid($auth_token);
        $permissionRepo->checkIfUserCanDoOperation($auth_token, 'update');

        $ticketValidator->checkIfPayloadFormatIsValid($valuesToUpdate);
        $ticketValidator->checkIfTicketNameIsValid($name);

        $ticket = $ticketRepo->updateTicket($ticketId, $valuesToUpdate);
        return $this->respondWithData($ticket);
    }
}
