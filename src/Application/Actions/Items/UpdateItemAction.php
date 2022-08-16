<?php

namespace App\Application\Actions\Items;

use App\Domain\DomainException\DomainPayloadStructureValidatorException;
use App\Domain\Item\Exception\ItemNameFormatException;
use App\Domain\Item\Exception\ItemStatusException;
use App\Domain\Permission\Exception\PermissionAuthTokenException;
use App\Domain\Permission\Exception\PermissionNoAuthorizationException;
use App\Domain\Permission\Permission;
use App\Domain\User\Exception\UserNoAuthorizationException;
use App\Domain\User\Exception\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class UpdateItemAction extends ItemAction
{
    /**
     * @throws ItemStatusException
     * @throws UserNotFoundException
     * @throws PermissionAuthTokenException
     * @throws UserNoAuthorizationException
     * @throws PermissionNoAuthorizationException
     * @throws ItemNameFormatException
     * @throws HttpBadRequestException
     * @throws DomainPayloadStructureValidatorException
     */
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();
        $itemId = (int) $this->resolveArg('id');

        $data = $this->getFormData();
        $name = $data['name'] ?? null;
        $statusName = $data['statusName'] ?? null;

        $permissionRepo = $this->permissionRepo;
        $itemValidator = $this->itemValidator;
        $itemRepo = $this->itemRepository;

        (new Permission($permissionRepo))->checkIfHasAccess($auth_token, 'update');

        $statusId = null;

        if (!$name && !$statusName) {
            $args = compact('name', 'statusName');
            $itemValidator->checkIfPayloadStructureIsValid($args);
        }

        if ($name) {
            $itemValidator->checkIfItemNameIsValid($name);
        }

        if ($statusName) {
            $statusId = $permissionRepo->checkIfItemStatusIsValidAndReturnId($statusName);
        }

        $item = $itemRepo->updateItem($itemId, $name, $statusId);
        return $this->respondWithData($item);
    }
}
