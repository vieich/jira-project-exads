<?php

namespace App\Application\Actions\Items;

use Psr\Http\Message\ResponseInterface as Response;

class CreateItemAction extends ItemAction
{
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();

        $data = $this->getFormData();
        $name = $data['name'] ?? null;
        $section_id = $data['section_id'] ?? null;

        $args = compact('name', 'section_id');

        $permissionRepo = $this->permissionRepo;
        $itemValidator = $this->itemValidator;
        $itemRepo = $this->itemRepository;

        $itemValidator->checkIfHeaderIsMissing($auth_token);
        $permissionRepo->checkIfAuthTokenIsValid($auth_token);
        $permissionRepo->checkIfUserCanDoOperation($auth_token, 'create');

        $itemValidator->checkIfPayloadFormatIsValid($args);
        $itemValidator->checkIfItemNameIsValid($name);
        $itemValidator->checkIfSectionIdIsValid($section_id);

        $action = $itemRepo->createItem($name, $section_id);
        return $this->respondWithData($action);
    }
}