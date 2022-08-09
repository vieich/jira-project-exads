<?php

namespace App\Application\Actions\Section;

use Psr\Http\Message\ResponseInterface as Response;

class ViewSectionAction extends SectionAction
{

    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();
        $sectionId = (int) $this->resolveArg('id');

        $permissionRepo = $this->permissionRepository;
        $sectionValidator = $this->sectionValidator;
        $sectionRepo = $this->sectionRepository;

        $sectionValidator->checkIfHeaderIsMissing($auth_token);
        $permissionRepo->checkIfAuthTokenIsValid($auth_token);
        $permissionRepo->checkIfUserCanDoOperation($auth_token, 'read');

        $section = $sectionRepo->findSectionById($sectionId);
        return $this->respondWithData($section);
    }
}