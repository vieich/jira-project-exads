<?php

namespace App\Application\Actions\Items;

use Psr\Http\Message\ResponseInterface as Response;

class ListItemAction extends ItemAction
{

    protected function action(): Response
    {
        $itemRepository = $this->itemRepository;

        $items = $itemRepository->findAll();
        return $this->respondWithData($items);
    }
}