<?php

namespace App\Application\Actions\Section;

use Psr\Http\Message\ResponseInterface as Response;

class CreateSectionAction extends SectionAction
{

    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();

        $data = $this->getFormData();
        $name = $data['name'] ?? null;
        $tab_id = $data['tab_id'] ?? null;

        $args = compact('name', 'tab_id');

        $permissionRepo = $this->permissionRepository;
        $sectionValidator = $this->sectionValidator;
        $sectionRepo = $this->sectionRepository;

        $sectionValidator->checkIfHeaderIsMissing($auth_token);
        $permissionRepo->checkIfAuthTokenIsValid($auth_token);
        $permissionRepo->checkIfUserCanDoOperation($auth_token, 'create');

        $sectionValidator->checkIfPayloadStructureIsValid($args);
        $sectionValidator->checkIfSectionNameIsValid($name);
        $sectionValidator->checkIfTabIdIsValid($tab_id);

        $section = $sectionRepo->createSection($name, $tab_id);
        return $this->respondWithData($section);
    }
}