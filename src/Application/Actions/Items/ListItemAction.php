<?php

namespace App\Application\Actions\Items;

use App\Domain\DomainException\DomainDataFormatException;
use App\Domain\Permission\Exception\PermissionAuthTokenException;
use App\Domain\Permission\Exception\PermissionNoAuthorizationException;
use App\Domain\Permission\Permission;
use App\Domain\User\Exception\UserNoAuthorizationException;
use App\Domain\User\Exception\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;

class ListItemAction extends ItemAction
{

    /**
     * @throws UserNotFoundException
     * @throws PermissionNoAuthorizationException
     * @throws PermissionAuthTokenException
     * @throws DomainDataFormatException
     */
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();
        $data = $this->getFormData();
        $showDeleted = $data['showDeleted'] ?? false;
        $operation[] = 'read';

        $itemRepository = $this->itemRepository;

        if ($showDeleted) {
            $this->itemValidator->checkIfShowDeletedIsValid($showDeleted);
            $operation[] = 'showDeleted';
        }

        (new Permission($this->permissionRepo))->checkIfHasAccess($auth_token, $operation);

        $items = $itemRepository->findAll($showDeleted);

        $this->logger->info('List of items viewed.');

        return $this->respondWithData($items);
    }
}
