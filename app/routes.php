<?php
declare(strict_types=1);

use App\Application\Actions\Items\ListItemAction;
use App\Application\Actions\Items\ViewItemAction;
use App\Application\Actions\Section\CreateSectionAction;
use App\Application\Actions\Section\DeleteSectionAction;
use App\Application\Actions\Section\ListSectionAction;
use App\Application\Actions\Section\UpdateSectionAction;
use App\Application\Actions\Section\ViewSectionAction;
use App\Application\Actions\Tab\CreateTabAction;

use App\Application\Actions\Tab\DeleteTabAction;
use App\Application\Actions\Tab\ListTabAction;
use App\Application\Actions\Tab\UpdateTabAction;
use App\Application\Actions\Tab\ViewTabAction;
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
use Slim\Exception\HttpNotFoundException;
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
        $group->post('', CreateUserAction::class);
        $group->patch('/{id}', UpdateUserAction::class);
        $group->delete('/{username}', DeleteUserAction::class);
        $group->post('/login', LoginUserAction::class);
    });

    $app->group('/tickets', function (Group $group) {
        $group->get('', ListTicketAction::class);
        $group->get('/{id}', ViewTicketAction::class);
        $group->post('', CreateTicketAction::class);
        $group->delete('/{id}', DeleteTicketAction::class);
        $group->patch('/{id}', UpdateTicketAction::class);
    });

    $app->group('/tabs', function (Group $group) {
        $group->post('', CreateTabAction::class);
        $group->get('', ListTabAction::class);
        $group->get('/{id}', ViewTabAction::class);
        $group->delete('/{id}', DeleteTabAction::class);
        $group->patch('/{id}', UpdateTabAction::class);
    });

    $app->group('/sections', function (Group $group) {
        $group->post('', CreateSectionAction::class);
        $group->get('', ListSectionAction::class);
        $group->get('/{id}', ViewSectionAction::class);
        $group->delete('/{id}', DeleteSectionAction::class);
        $group->patch('/{id}', UpdateSectionAction::class);
    });

    $app->group('/items', function (Group $group) {
        $group->post('', CreateSectionAction::class);
        $group->get('', ListItemAction::class);
        $group->get('/{id}', ViewItemAction::class);
        $group->delete('/{id}', DeleteSectionAction::class);
        $group->patch('/{id}', UpdateSectionAction::class);
    });

};
