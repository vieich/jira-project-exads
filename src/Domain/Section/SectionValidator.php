<?php

namespace App\Domain\Section;

use App\Domain\Section\Exception\SectionNameFormatException;
use App\Domain\Section\Exception\SectionTabIdFormatException;
use App\Domain\Validator;

class SectionValidator extends Validator
{
    /**
     * @throws SectionNameFormatException
     */
    public function checkIfSectionNameIsValid($tabName): void
    {
        if (!preg_match("/^\\w{3,8}$/", $tabName)) {
            throw new SectionNameFormatException();
        }
    }

    /**
     * @throws SectionTabIdFormatException
     */
    public function checkIfTabIdIsValid($tabId): void
    {
        if (!is_int($tabId)) {
            throw new SectionTabIdFormatException();
        }
    }
}
