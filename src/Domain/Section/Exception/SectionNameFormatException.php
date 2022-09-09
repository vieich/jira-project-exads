<?php

namespace App\Domain\Section\Exception;

use App\Domain\DomainException\DomainDataFormatException;

class SectionNameFormatException extends DomainDataFormatException
{
    public $message = 'Section name invalid, only letters, numbers and _ accepted, and must have 3 to 13 characters';

}