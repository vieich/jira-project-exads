<?php
declare(strict_types=1);

use App\Application\Actions\Tab\CreateTabAction;

use App\Application\Actions\Ticket\DeleteTicketAction;
use App\Application\Actions\Ticket\ListTicketAction;
use App\Application\Actions\Ticket\ViewTicketAction;
use App\Application\Actions\Ticket\UpdateTicketAction;
use App\Application\Actions\Ticket\CreateTicketAction;

use App\Application\Actions\User\CreateUserAction;
use App\Application\Actions\User\DeleteUserAction;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\LoginUserAction;
use App\Application\Actions\User\UpdateUserAction;
use App\Application\Actions\User\ViewUserAction;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
        $group->post('/create', CreateUserAction::class);
        $group->patch('/update/{id}', UpdateUserAction::class);
        $group->delete('/delete', DeleteUserAction::class);
        $group->post('/login', LoginUserAction::class);
    });

    $app->group('/tickets', function (Group $group) {
        $group->get('/', ListTicketAction::class);
        $group->get('/{id}', ViewTicketAction::class);
        $group->post('/create', CreateTicketAction::class);
        $group->delete('/delete/{id}', DeleteTicketAction::class);
        $group->patch('/ticket/{id}', UpdateTicketAction::class);
    });

    $app->group('/tabs', function (Group $group) {
        $group->post('/create', CreateTabAction::class);
    });
};
