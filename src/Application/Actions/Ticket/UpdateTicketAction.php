<?php

namespace App\Application\Actions\Ticket;

use App\Domain\Ticket\Exception\TicketPayloadStructureException;
use App\Domain\User\Exception\UserNoAuthorizationException;
use Psr\Http\Message\ResponseInterface as Response;

class UpdateTicketAction extends TicketAction
{
    protected function action(): Response
    {
        $ticketId = (int) $this->resolveArg('id');

        $data = json_decode(file_get_contents('php://input'), true);
        $ticketName = $data['name'] ?? null;
        $ticketIsDone = $data['is_done'] ?? null;
        $ticketRequesterUsername = $data['requester_name'] ?? null;
        $ticketRequesterPassword = $data['requester_password'] ?? null;

        $valuesToUpdate = [];

        if (!isset($ticketRequesterUsername) || !isset($ticketRequesterPassword)) {
            throw new TicketPayloadStructureException('You must pass your username and password');
        }

        if (!$this->permissionRepo->checkIfUserPasswordIsCorrect($ticketRequesterUsername, $ticketRequesterPassword)) {
            throw new UserNoAuthorizationException('Wrong username or password.');
        }

        if (!$this->permissionRepo->checkIfUserCanDoOperation($ticketRequesterUsername, 'update')) {
            throw new UserNoAuthorizationException('You dont have rights for that action, you must be an Admin.');
        }

        if (!isset($ticketName) && !isset($ticketIsDone)) {
            throw new TicketPayloadStructureException('No values found to update ticket.');
        }

        if (isset($ticketName)) {
            $valuesToUpdate['name'] = $ticketName;
        }

        if (isset($ticketIsDone)) {
            $valuesToUpdate['isDone'] = $ticketIsDone;
        }

        $ticket = $this->ticketRepository->updateTicket($ticketId, $valuesToUpdate);
    }
}
