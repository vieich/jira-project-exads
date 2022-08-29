<?php

namespace App\Application\Actions\Items;

use App\Domain\DomainException\DomainPayloadStructureValidatorException;
use App\Domain\Item\Exception\ItemNameFormatException;
use App\Domain\Item\Exception\ItemSectionIdFormatException;
use App\Domain\Permission\Exception\PermissionAuthTokenException;
use App\Domain\Permission\Permission;
use App\Domain\Permission\Exception\PermissionNoAuthorizationException;
use App\Domain\User\Exception\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;

class CreateItemAction extends ItemAction
{
    /**
     * @throws ItemSectionIdFormatException
     * @throws UserNotFoundException
     * @throws PermissionAuthTokenException
     * @throws ItemNameFormatException
     * @throws DomainPayloadStructureValidatorException|PermissionNoAuthorizationException
     */
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();

        $data = $this->getFormData();
        $itemName = $data['itemName'] ?? null;
        $sectionId = $data['sectionId'] ?? null;
        $operation[] = 'create';

        $args = compact('itemName', 'sectionId');

        $itemValidator = $this->itemValidator;
        $itemRepo = $this->itemRepository;

        (new Permission($this->permissionRepo))->checkIfHasAccess($auth_token, $operation);

        $itemValidator->checkIfPayloadStructureIsValid($args);
        $itemValidator->checkIfItemNameIsValid($itemName);
        $itemValidator->checkIfSectionIdIsValid($sectionId);

        $item = $itemRepo->createItem($itemName, $sectionId);

        $this->logger->info('Item with id ' . $item->getId() . ' created.');

        return $this->respondWithData($item);
    }
}