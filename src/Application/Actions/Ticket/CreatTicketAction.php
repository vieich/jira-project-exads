<?php

namespace App\Application\Actions\Ticket;

use App\Domain\Ticket\Exception\TicketPayloadDataException;
use App\Domain\Ticket\Exception\TicketPayloadStructureException;
use App\Domain\User\UserNoAuthorizationException;
use Psr\Http\Message\ResponseInterface as Response;

class CreatTicketAction extends TicketAction
{
    protected function action(): Response
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $ticketName = $data['name'] ?? null;
        $ticketCreatorId = $data['creator_id'] ?? null;
        $ticketCreatorName = $data['creator_name'] ?? null;
        $ticketCreatorPassword = $data['creator_password'] ?? null;

        if (!isset($ticketName) || !isset($ticketCreatorName) || !isset($ticketCreatorPassword)) {
            throw new TicketPayloadStructureException('Payload does not meet the requirements');
        }

        if (!TicketValidator::getInstance()->isNameValid($ticketName)) {
            throw new TicketPayloadDataException('Ticket name does not meet the requirements');
        }

        if (!$this->permissionRepo->checkIfUserPasswordIsCorrect($ticketCreatorName, $ticketCreatorPassword)) {
            throw new UserNoAuthorizationException('Wrong username or password');
        }

        if (!$this->permissionRepo->checkIfUserRoleGivesAccess($ticketCreatorName, 'post')) {
            throw new UserNoAuthorizationException('The user doesnt have rights for that action');
        }

        $ticket = $this->ticketRepository->createTicket($ticketName, $ticketCreatorId);

        return $this->respondWithData($ticket);
    }
}
