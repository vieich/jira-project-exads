<?php

namespace App\Application\Actions\Tab;

use App\Domain\Tab\Exception\TabNameFormatException;
use App\Domain\Tab\Exception\TabNoAuthorizationException;
use App\Domain\Tab\Exception\TabPayloadStructureException;

class TabValidator
{
    public function checkIfHeaderIsMissing(string $header): void
    {
        if($header == "") {
            throw new TabNoAuthorizationException('Auth-Token is missing on the header.');
        }
    }

    public function checkIfPayloadFormatIsValid(array $args): void
    {
        foreach ($args as $key => $value) {
            if (!isset($value) || $value == "") {
                throw new TabPayloadStructureException('Payload is not valid, is missing the ' . $key . ' field');
            }
        }
    }

    public function checkIfTabNameIsValid($tabName): void
    {
        if (!preg_match("/^[A-Za-z0-9 ]{2,8}$/", $tabName)) {
            throw new TabNameFormatException();
        }
    }
}