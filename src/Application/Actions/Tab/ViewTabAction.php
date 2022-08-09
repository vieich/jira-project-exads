<?php

namespace App\Application\Actions\Tab;

use Psr\Http\Message\ResponseInterface as Response;

class ViewTabAction extends TabAction
{

    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();
        $tabId = (int) $this->resolveArg('id');

        $permissionRepo = $this->permissionRepository;
        $tabRepo = $this->tabRepository;
        $tabValidator = $this->tabValidator;

        $tabValidator->checkIfHeaderIsMissing($auth_token);
        $permissionRepo->checkIfAuthTokenIsValid($auth_token);
        $permissionRepo->checkIfUserCanDoOperation($auth_token, 'read');

        $tab = $tabRepo->findTabById($tabId);
        return $this->respondWithData($tab);
    }
}