<?php

namespace App\Application\Actions\Tab;

use App\Application\Actions\Validator;
use App\Domain\Tab\Exception\TabNameFormatException;

class TabValidator extends Validator
{

    public function checkIfTabNameIsValid($tabName): void
    {
        if (!preg_match("/^[A-Za-z0-9 ]{2,8}$/", $tabName)) {
            throw new TabNameFormatException();
        }
    }
}