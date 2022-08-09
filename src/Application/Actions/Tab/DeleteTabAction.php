<?php

namespace App\Application\Actions\Tab;

use Psr\Http\Message\ResponseInterface as Response;

class DeleteTabAction extends TabAction
{
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();
        $tabId = (int) $this->resolveArg('id');

        $tabValidator = $this->tabValidator;
        $permissionRepo = $this->permissionRepository;
        $tabRepo = $this->tabRepository;

        $tabValidator->checkIfHeaderIsMissing($auth_token);
        $permissionRepo->checkIfAuthTokenIsValid($auth_token);
        $permissionRepo->checkIfUserCanDoOperation($auth_token, 'delete');

        $action = $tabRepo->deleteTabById($tabId);
        return $this->respondWithData($action);
    }
}