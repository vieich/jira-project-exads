<?php

namespace App\Application\Actions\Tab;

use phpDocumentor\Reflection\Types\This;
use Psr\Http\Message\ResponseInterface as Response;

class UpdateTabAction extends TabAction
{

    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();
        $tabId = (int) $this->resolveArg('id');

        $data = $this->getFormData();
        $name = $data['name'] ?? null;

        $args = compact('name');

        $tabValidator = $this->tabValidator;
        $tabRepo = $this->tabRepository;
        $permissonRepo = $this->permissionRepository;

        $tabValidator->checkIfHeaderIsMissing($auth_token);
        $permissonRepo->checkIfAuthTokenIsValid($auth_token);
        $permissonRepo->checkIfUserCanDoOperation($auth_token, 'update');

        $tabValidator->checkIfPayloadFormatIsValid($args);
        $tabValidator->checkIfTabNameIsValid($name);

        $tab = $tabRepo->updateTab($tabId, $name);
        return $this->respondWithData($tab);

    }
}