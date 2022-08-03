<?php

namespace App\Application\Actions\Ticket;

use App\Application\Actions\Permission\PermissionValidator;
use App\Infrastructure\Persistence\Permission\PermissionRepo;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class ListTicketAction extends TicketAction
{

    protected function action(): Response
    {
        $headers = apache_request_headers();
        $auth_token = $headers['Auth-Token'] ?? null;

        $permissionValidator = PermissionValidator::getInstance();
        $permissionRepo = $this->permissionRepo;

        $permissionValidator->checkIfAuthTokenFormatIsValid($auth_token);
        $permissionRepo->checkIfAuthTokenIsValid($auth_token);

        $permissionRepo->checkIfUserCanDoOperation($auth_token,'read');

        return $this->respondWithData([]);
    }
}
