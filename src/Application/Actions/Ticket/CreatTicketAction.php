<?php

namespace App\Application\Actions\Ticket;

use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class CreatTicketAction extends TicketAction
{

    protected function action(): Response
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $ticketName = $data['ticket_name'];
        $ticketCreator = $data['ticket_creator'];

        $ticket = $this->ticketRepository->createTicket($ticketName, $ticketCreator);

        return $this->respondWithData($ticket);
    }
}
