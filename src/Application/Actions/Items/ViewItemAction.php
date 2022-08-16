<?php

namespace App\Application\Actions\Items;

use App\Domain\Permission\Exception\PermissionAuthTokenException;
use App\Domain\Permission\Exception\PermissionNoAuthorizationException;
use App\Domain\Permission\Permission;
use App\Domain\User\Exception\UserNoAuthorizationException;
use App\Domain\User\Exception\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class ViewItemAction extends ItemAction
{
    /**
     * @throws UserNotFoundException
     * @throws PermissionNoAuthorizationException
     * @throws PermissionAuthTokenException
     * @throws HttpBadRequestException
     * @throws UserNoAuthorizationException
     */
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();
        $itemId = (int) $this->resolveArg('id');

        $itemRepo = $this->itemRepository;

        (new Permission($this->permissionRepo))->checkIfHasAccess($auth_token, 'read');

        $item = $itemRepo->findItemById($itemId);
        return $this->respondWithData($item);




    }
}