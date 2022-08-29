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
        $itemName = $data['itemName'] ?? null;
        $statusName = $data['statusName'] ?? null;
        $operation[] = 'update';

        $permissionRepo = $this->permissionRepo;
        $itemValidator = $this->itemValidator;
        $itemRepo = $this->itemRepository;

        (new Permission($permissionRepo))->checkIfHasAccess($auth_token, $operation);

        $statusId = null;

        if (!$itemName && !$statusName) {
            $args = compact('itemName', 'statusName');
            $itemValidator->checkIfPayloadStructureIsValid($args);
        }

        if ($itemName) {
            $itemValidator->checkIfItemNameIsValid($itemName);
        }

        if ($statusName) {
            $statusId = $permissionRepo->checkIfItemStatusIsValidAndReturnId($statusName);
        }

        $item = $itemRepo->updateItem($itemId, $itemName, $statusId);

        $this->logger->info(' Item with id ' . $itemId . ' was updated.');

        return $this->respondWithData($item);
    }
}
