<?php

namespace App\Domain\Item;

use App\Domain\Item\Exception\ItemNameFormatException;
use App\Domain\Item\Exception\ItemSectionIdFormatException;
use App\Domain\Validator;

class ItemValidator extends Validator
{
    /**
     * @throws ItemNameFormatException
     */
    public function checkIfItemNameIsValid($itemName): void
    {
        if (!preg_match("/^\\w{3,13}$/", $itemName)) {
            throw new ItemNameFormatException();
        }
    }

    /**
     * @throws ItemSectionIdFormatException
     */
    public function checkIfSectionIdIsValid($sectionId): void
    {
        if (!is_int($sectionId)) {
            throw new ItemSectionIdFormatException();
        }
    }
}
