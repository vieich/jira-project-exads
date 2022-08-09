<?php

namespace App\Application\Actions\Items;

use Psr\Http\Message\ResponseInterface as Response;

class ViewItemAction extends ItemAction
{
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();
        $sectionId = (int) $this->resolveArg('id');

        $permissionRepo = $this->permissionRepo;
        $itemValidator = $this->itemValidator;
        $itemRepo = $this->itemRepository;

        $itemValidator->checkIfHeaderIsMissing($auth_token);
        $permissionRepo->checkIfAuthTokenIsValid($auth_token);
        $permissionRepo->checkIfUserCanDoOperation($auth_token, 'read');

        $item = $itemRepo->findItemById($sectionId);
        return $this->respondWithData($item);




    }
}