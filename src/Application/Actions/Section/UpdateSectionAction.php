<?php

namespace App\Application\Actions\Section;

use Psr\Http\Message\ResponseInterface as Response;

class UpdateSectionAction extends SectionAction
{

    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();
        $sectionId = (int) $this->resolveArg('id');

        $data = $this->getFormData();
        $name = $data['name'] ?? null;

        $args = compact('name');

        $permissionRepo = $this->permissionRepository;
        $sectionValidator = $this->sectionValidator;
        $sectionRepo = $this->sectionRepository;

        $sectionValidator->checkIfHeaderIsMissing($auth_token);
        $permissionRepo->checkIfAuthTokenIsValid($auth_token);
        $permissionRepo->checkIfUserCanDoOperation($auth_token, 'update');

        $sectionValidator->checkIfPayloadFormatIsValid($args);
        $sectionValidator->checkIfSectionNameIsValid($name);

        $action = $sectionRepo->updateSection($sectionId, $name);
        return $this->respondWithData($action);
    }
}