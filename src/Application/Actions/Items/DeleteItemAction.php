<?php

namespace App\Application\Actions\Items;

use Psr\Http\Message\ResponseInterface as Response;

class DeleteItemAction extends ItemAction
{
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();
        $sectionId = (int) $this->resolveArg('id');

        $permissionRepo = $this->permissionRepo;
        $itemRepo = $this->itemRepository;
        $itemValidator = $this->itemValidator;

        $itemValidator->checkIfHeaderIsMissing($auth_token);
        $permissionRepo->checkIfAuthTokenIsValid($auth_token);
        $permissionRepo->checkIfUserCanDoOperation($auth_token, 'delete');

        $action = $itemRepo->deleteItem($sectionId);
        return $this->respondWithData($action);
    }
}