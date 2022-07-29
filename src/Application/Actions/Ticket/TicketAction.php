<?php

namespace App\Application\Actions\Ticket;

use App\Application\Actions\Action;
use App\Domain\Ticket\TicketRepository;
use Psr\Log\LoggerInterface;

abstract class TicketAction extends Action
{
    protected $ticketRepository;

    public function __construct(LoggerInterface $logger, TicketRepository $ticketRepository)
    {
        parent::__construct($logger);
        $this->ticketRepository = $ticketRepository;
    }

}