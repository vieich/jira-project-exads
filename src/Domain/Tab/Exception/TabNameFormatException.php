<?php

namespace App\Domain\Tab\Exception;

use App\Domain\DomainException\DomainDataFormatException;

class TabNameFormatException extends DomainDataFormatException
{
    public $message = 'Tab name does not accept special characters, and must have 3 to 8 characters';
}