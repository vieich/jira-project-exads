<?php

namespace App\Application\Actions\Ticket;

use Psr\Http\Message\ResponseInterface as Response;

class CreatTicketAction extends TicketAction
{

    protected function action(): Response
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $ticketName = $data['ticket_name'];
        $ticketCreatorId = $data['ticket_creator_id'];
        $ticketCreatorToken = $data['ticket_creator_token'];

        $ticket = $this->ticketRepository->createTicket($ticketName, $ticketCreatorId, $ticketCreatorToken);

        return $this->respondWithData($ticket);
    }
}
