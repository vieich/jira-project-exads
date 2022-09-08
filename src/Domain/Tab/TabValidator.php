<?php

namespace App\Domain\Tab;

use App\Domain\Tab\Exception\TabNameFormatException;
use App\Domain\Validator;

class TabValidator extends Validator
{

    /**
     * @throws TabNameFormatException
     */
    public function checkIfTabNameIsValid($tabName): void
    {
        if (!preg_match("/^\\w{3,8}$/", $tabName)) {
            throw new TabNameFormatException();
        }
    }
}