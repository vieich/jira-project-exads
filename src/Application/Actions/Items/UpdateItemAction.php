<?php

namespace App\Application\Actions\Items;

use Psr\Http\Message\ResponseInterface as Response;

class UpdateItemAction extends ItemAction
{
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

        $itemValidator->checkIfHeaderIsMissing($auth_token);
        $permissionRepo->checkIfAuthTokenIsValid($auth_token);
        $permissionRepo->checkIfUserCanDoOperation($auth_token, 'update');

        $statusId = null;

        if (!$name && !$statusName) {
            $args = compact('name', 'statusName');
            $itemValidator->checkIfPayloadFormatIsValid($args);
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
