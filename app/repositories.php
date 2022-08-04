<?php
declare(strict_types=1);

use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\User\UserRepo;
use App\Domain\Ticket\TicketRepository;
use App\Infrastructure\Persistence\Ticket\TicketRepo;
use App\Domain\Tab\TabRepository;
use App\Infrastructure\Persistence\Tab\TabRepo;

use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    // Here we map our UserRepo interface to its in memory implementation
    $containerBuilder->addDefinitions([
        UserRepository::class => \DI\autowire(UserRepo::class),
        TicketRepository::class => \DI\autowire(TicketRepo::class),
        TabRepository::class => \DI\autowire(TabRepo::class)
    ]);
};
