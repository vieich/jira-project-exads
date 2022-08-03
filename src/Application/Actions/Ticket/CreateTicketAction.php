<?php

namespace App\Application\Actions\Ticket;

use App\Application\Actions\Permission\PermissionValidator;
use App\Domain\Ticket\Exception\TicketPayloadDataException;
use App\Domain\Ticket\Exception\TicketPayloadStructureException;
use App\Domain\User\Exception\UserNoAuthorizationException;
use Psr\Http\Message\ResponseInterface as Response;

class CreateTicketAction extends TicketAction
{
    protected function action(): Response
    {
        $headers = apache_request_headers();
        $auth_token = $headers['Auth-Token'] ?? null;

        $data = json_decode(file_get_contents('php://input'), true);
        $ticket_name = $data['ticket_name'] ?? null;

        $args = compact('ticket_name');

        $ticketValidator = TicketValidator::getInstance();
        $permissionValidator = PermissionValidator::getInstance();

        $ticketRepo = $this->ticketRepository;
        $permissionRepo = $this->permissionRepo;

        $ticketValidator->checkIfPayloadIsValid($args);
        $ticketValidator->checkIfTicketNameIsValid($ticket_name);

        $permissionValidator->checkIfAuthTokenFormatIsValid($auth_token);

        $permissionRepo->checkIfAuthTokenIsValid($auth_token);
        $permissionRepo->checkIfUserCanDoOperation($auth_token, 'create');
        $user = $permissionRepo->getUserByToken($auth_token);

        $ticket = $ticketRepo->createTicket($ticket_name, $user->getId());
        return $this->respondWithData($ticket);
    }
}
