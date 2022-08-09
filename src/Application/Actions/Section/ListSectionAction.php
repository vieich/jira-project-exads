<?php

namespace App\Application\Actions\Section;

use Psr\Http\Message\ResponseInterface as Response;

class ListSectionAction extends SectionAction
{
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();

        $permissionRepo = $this->permissionRepository;
        $sectionValidator = $this->sectionValidator;
        $sectionRepo = $this->sectionRepository;

        $sectionValidator->checkIfHeaderIsMissing($auth_token);

        $permissionRepo->checkIfAuthTokenIsValid($auth_token);
        $permissionRepo->checkIfUserCanDoOperation($auth_token, 'read');

        $sections = $sectionRepo->findAll();
        return $this->respondWithData($sections);
    }
}