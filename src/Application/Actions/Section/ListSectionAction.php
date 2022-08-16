<?php

namespace App\Application\Actions\Section;

use App\Domain\Permission\Exception\PermissionAuthTokenException;
use App\Domain\Permission\Exception\PermissionNoAuthorizationException;
use App\Domain\Permission\Permission;
use App\Domain\User\Exception\UserNoAuthorizationException;
use App\Domain\User\Exception\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;

class ListSectionAction extends SectionAction
{
    /**
     * @throws UserNotFoundException
     * @throws PermissionNoAuthorizationException
     * @throws PermissionAuthTokenException
     * @throws UserNoAuthorizationException
     */
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();

        $sectionRepo = $this->sectionRepository;

        (new Permission($this->permissionRepository))->checkIfHasAccess($auth_token, 'read');

        $sections = $sectionRepo->findAll();
        return $this->respondWithData($sections);
    }
}
