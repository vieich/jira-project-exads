<?php

namespace App\Application\Actions\Section;

use App\Domain\DomainException\DomainPayloadStructureValidatorException;
use App\Domain\Permission\Exception\PermissionAuthTokenException;
use App\Domain\Permission\Exception\PermissionNoAuthorizationException;
use App\Domain\Permission\Permission;
use App\Domain\Section\Exception\SectionNameFormatException;
use App\Domain\User\Exception\UserNoAuthorizationException;
use App\Domain\User\Exception\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class UpdateSectionAction extends SectionAction
{

    /**
     * @throws UserNotFoundException
     * @throws PermissionAuthTokenException
     * @throws UserNoAuthorizationException
     * @throws SectionNameFormatException
     * @throws PermissionNoAuthorizationException
     * @throws HttpBadRequestException
     * @throws DomainPayloadStructureValidatorException
     */
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();
        $sectionId = (int) $this->resolveArg('id');

        $data = $this->getFormData();
        $name = $data['name'] ?? null;

        $args = compact('name');

        $sectionValidator = $this->sectionValidator;
        $sectionRepo = $this->sectionRepository;

        (new Permission($this->permissionRepository))->checkIfHasAccess($auth_token, 'update');

        $sectionValidator->checkIfPayloadStructureIsValid($args);
        $sectionValidator->checkIfSectionNameIsValid($name);

        $action = $sectionRepo->updateSection($sectionId, $name);
        return $this->respondWithData($action);
    }
}
