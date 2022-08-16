<?php

namespace App\Domain\Tab;

use App\Domain\Tab\Exception\TabNameFormatException;
use App\Domain\Validator;

class TabValidator extends Validator
{

    public function checkIfTabNameIsValid($tabName): void
    {
        if (!preg_match("/^[A-Za-z0-9 ]{2,8}$/", $tabName)) {
            throw new TabNameFormatException();
        }
    }
}