<?php

namespace App\Application\Actions\Ticket;

use App\Application\Actions\Action;
use App\Domain\Paginator;
use App\Domain\Ticket\TicketPaginator;
use App\Domain\Ticket\TicketRepository;
use App\Domain\Ticket\TicketValidator;
use App\Infrastructure\Persistence\Permission\PermissionRepo;
use Psr\Log\LoggerInterface;

abstract class TicketAction extends Action
{
    protected $ticketRepository;
    protected $permissionRepository;
    protected $ticketValidator;
    protected $ticketPaginator;

    public function __construct(
        LoggerInterface $logger,
        TicketRepository $ticketRepository,
        PermissionRepo $permissionRepo,
        TicketValidator $ticketValidator,
        TicketPaginator $ticketPaginator
    ) {
        parent::__construct($logger);
        $this->ticketRepository = $ticketRepository;
        $this->permissionRepository = $permissionRepo;
        $this->ticketValidator = $ticketValidator;
        $this->ticketPaginator = $ticketPaginator;
    }
}
