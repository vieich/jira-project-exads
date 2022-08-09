<?php

namespace App\Application\Actions\Tab;

use Psr\Http\Message\ResponseInterface as Response;

class ListTabAction extends TabAction
{
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();

        $permissionRepo = $this->permissionRepository;
        $tabValidator = $this->tabValidator;
        $tabRepo = $this->tabRepository;

        $tabValidator->checkIfHeaderIsMissing($auth_token);
        $permissionRepo->checkIfAuthTokenIsValid($auth_token);
        $permissionRepo->checkIfUserCanDoOperation($auth_token, "read");

        $tabs = $tabRepo->findAll();
        return $this->respondWithData($tabs);
    }
}