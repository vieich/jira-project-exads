<?php

namespace App\Domain\Section;

use App\Domain\Section\Exception\SectionNameFormatException;
use App\Domain\Section\Exception\SectionTabIdFormatException;
use App\Domain\Validator;

class SectionValidator extends Validator
{
    public function checkIfSectionNameIsValid($tabName): void
    {
        if (!preg_match("/^[A-Za-z0-9 ]{2,8}$/", $tabName)) {
            throw new SectionNameFormatException();
        }
    }

    public function checkIfTabIdIsValid($tabId): void
    {
        if (!is_int($tabId)) {
            throw new SectionTabIdFormatException();
        }
    }
}
