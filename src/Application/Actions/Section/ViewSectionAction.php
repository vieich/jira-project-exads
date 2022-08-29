<?php

namespace App\Application\Actions\Section;

use App\Domain\Permission\Exception\PermissionAuthTokenException;
use App\Domain\Permission\Exception\PermissionNoAuthorizationException;
use App\Domain\Permission\Permission;
use App\Domain\User\Exception\UserNoAuthorizationException;
use App\Domain\User\Exception\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class ViewSectionAction extends SectionAction
{

    /**
     * @throws UserNotFoundException
     * @throws PermissionNoAuthorizationException
     * @throws PermissionAuthTokenException
     * @throws HttpBadRequestException
     */
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();
        $sectionId = (int) $this->resolveArg('id');
        $operation[] = 'read';

        $sectionRepo = $this->sectionRepository;

        (new Permission($this->permissionRepository))->checkIfHasAccess($auth_token, $operation);

        $section = $sectionRepo->findSectionById($sectionId);

        $this->logger->info('Section with id ' . $sectionId . ' viewed.');

        return $this->respondWithData($section);
    }
}