<?php

namespace App\Application\Actions\Tab;

use Psr\Http\Message\ResponseInterface as Response;

class CreateTabAction extends TabAction
{

    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();

        $data = $this->getFormData();
        $name = $data['name'] ?? null;
        $ticket_id = $data['ticket_id'] ?? null;

        $args = compact('name', 'ticket_id');

        $tabValidator = TabValidator::getInstance();
        $tabRepo = $this->tabRepository;
        $permissonRepo = $this->permissionRepository;

        $tabValidator->checkIfHeaderIsMissing($auth_token);
        $permissonRepo->checkIfAuthTokenIsValid($auth_token);
        $permissonRepo->checkIfUserCanDoOperation($auth_token, 'create');

        $tabValidator->checkIfPayloadFormatIsValid($args);
        $tabValidator->checkIfTabNameIsValid($name);

        $tab = $tabRepo->createTab($name, $ticket_id);
        return $this->respondWithData($tab);
    }
}