<?php

namespace App\Application\Actions\Section;

use App\Domain\Permission\Exception\PermissionAuthTokenException;
use App\Domain\Permission\Exception\PermissionNoAuthorizationException;
use App\Domain\Permission\Permission;
use App\Domain\User\Exception\UserNoAuthorizationException;
use App\Domain\User\Exception\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class DeleteSectionAction extends SectionAction
{
    /**
     * @throws UserNotFoundException
     * @throws PermissionNoAuthorizationException
     * @throws HttpBadRequestException
     * @throws PermissionAuthTokenException
     * @throws UserNoAuthorizationException
     */
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();
        $sectionId = (int) $this->resolveArg('id');

        $sectionRepo = $this->sectionRepository;

        (new Permission($this->permissionRepository))->checkIfHasAccess($auth_token, 'delete');

        $action = $sectionRepo->deleteSection($sectionId);
        return $this->respondWithData($action);
    }
}
