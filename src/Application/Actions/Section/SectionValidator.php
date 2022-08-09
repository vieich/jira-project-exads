<?php

namespace App\Application\Actions\Section;

use App\Domain\Section\Exception\SectionNameFormatException;
use App\Domain\Section\Exception\SectionNoAuthorizationException;
use App\Domain\Section\Exception\SectionPayloadStructureException;
use App\Domain\Section\Exception\SectionTabIdFormatException;

class SectionValidator
{
    public function checkIfHeaderIsMissing(string $header): void
    {
        if ($header == "") {
            throw new SectionNoAuthorizationException('Auth-Token is missing on the header.');
        }
    }

    public function checkIfPayloadFormatIsValid(array $args): void
    {
        foreach ($args as $key => $value) {
            if (!isset($value) || $value == "") {
                throw new SectionPayloadStructureException('Payload is not valid, is missing the ' . $key . ' field');
            }
        }
    }

    public function checkIfSectionNameIsValid($tabName): void
    {
        if (!preg_match("/^[A-Za-z0-9 ]{2,8}$/", $tabName)) {
            throw new SectionNameFormatException();
        }
    }

    public function checkIfTabIdIsValid($tabId): void
    {
        if(!is_int($tabId)) {
            throw new SectionTabIdFormatException();
        }
    }
}
