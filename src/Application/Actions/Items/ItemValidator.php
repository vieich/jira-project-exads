<?php

namespace App\Application\Actions\Items;

use App\Domain\Item\Exception\ItemNameFormatException;
use App\Domain\Item\Exception\ItemNoAuthorizationException;
use App\Domain\Item\Exception\ItemPayloadStructureException;
use App\Domain\Item\Exception\ItemSectionIdFormatException;

class ItemValidator
{
    public function checkIfHeaderIsMissing(string $header): void
    {
        if ($header == "") {
            throw new ItemNoAuthorizationException('Auth-Token is missing on the header.');
        }
    }

    public function checkIfPayloadFormatIsValid(array $args): void
    {
        foreach ($args as $key => $value) {
            if (!isset($value) || $value == "") {
                throw new ItemPayloadStructureException('Payload is not valid, is missing the name or statusName field');
            }
        }
    }

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
