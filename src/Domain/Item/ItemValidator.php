<?php

namespace App\Domain\Item;

use App\Domain\Item\Exception\ItemNameFormatException;
use App\Domain\Item\Exception\ItemSectionIdFormatException;
use App\Domain\Validator;

class ItemValidator extends Validator
{
    public function checkIfItemNameIsValid($itemName): void
    {
        if (!preg_match("/^[A-Za-z0-9 ]{2,15}$/", $itemName)) {
            throw new ItemNameFormatException('Name format');
        }
    }

    public function checkIfSectionIdIsValid($sectionId): void
    {
        if (!is_int($sectionId)) {
            throw new ItemSectionIdFormatException();
        }
    }
}
