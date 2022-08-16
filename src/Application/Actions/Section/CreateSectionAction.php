<?php

namespace App\Application\Actions\Section;

use App\Domain\DomainException\DomainPayloadStructureValidatorException;
use App\Domain\Permission\Exception\PermissionAuthTokenException;
use App\Domain\Permission\Exception\PermissionNoAuthorizationException;
use App\Domain\Permission\Permission;
use App\Domain\Section\Exception\SectionNameFormatException;
use App\Domain\Section\Exception\SectionTabIdFormatException;
use App\Domain\User\Exception\UserNoAuthorizationException;
use App\Domain\User\Exception\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;

class CreateSectionAction extends SectionAction
{

    /**
     * @throws UserNotFoundException
     * @throws SectionTabIdFormatException
     * @throws PermissionAuthTokenException
     * @throws UserNoAuthorizationException
     * @throws SectionNameFormatException
     * @throws PermissionNoAuthorizationException
     * @throws DomainPayloadStructureValidatorException
     */
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();

        $data = $this->getFormData();
        $name = $data['name'] ?? null;
        $tabId = $data['tab_id'] ?? null;

        $args = compact('name', 'tabId');

        $sectionValidator = $this->sectionValidator;
        $sectionRepo = $this->sectionRepository;

        (new Permission($this->permissionRepository))->checkIfHasAccess($auth_token, 'create');

        $sectionValidator->checkIfPayloadStructureIsValid($args);
        $sectionValidator->checkIfSectionNameIsValid($name);
        $sectionValidator->checkIfTabIdIsValid($tabId);

        $section = $sectionRepo->createSection($name, $tabId);
        return $this->respondWithData($section);
    }
}