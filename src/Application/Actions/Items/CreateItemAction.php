<?php

namespace App\Application\Actions\Items;

use App\Domain\DomainException\DomainPayloadStructureValidatorException;
use App\Domain\Item\Exception\ItemNameFormatException;
use App\Domain\Item\Exception\ItemSectionIdFormatException;
use App\Domain\Permission\Exception\PermissionAuthTokenException;
use App\Domain\Permission\Permission;
use App\Domain\User\Exception\UserNoAuthorizationException;
use App\Domain\Permission\Exception\PermissionNoAuthorizationException;
use App\Domain\User\Exception\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;

class CreateItemAction extends ItemAction
{
    /**
     * @throws ItemSectionIdFormatException
     * @throws UserNotFoundException
     * @throws PermissionAuthTokenException
     * @throws UserNoAuthorizationException
     * @throws ItemNameFormatException
     * @throws DomainPayloadStructureValidatorException|PermissionNoAuthorizationException
     */
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();

        $data = $this->getFormData();
        $name = $data['name'] ?? null;
        $section_id = $data['section_id'] ?? null;

        $args = compact('name', 'section_id');

        $itemValidator = $this->itemValidator;
        $itemRepo = $this->itemRepository;

        (new Permission($this->permissionRepo))->checkIfHasAccess($auth_token, 'create');

        $itemValidator->checkIfPayloadStructureIsValid($args);
        $itemValidator->checkIfItemNameIsValid($name);
        $itemValidator->checkIfSectionIdIsValid($section_id);

        $action = $itemRepo->createItem($name, $section_id);
        return $this->respondWithData($action);
    }
}