<?php

namespace App\Domain\Section\Exception;

use App\Domain\DomainException\DomainDataFormatException;

class SectionNameFormatException extends DomainDataFormatException
{
    public $message = 'Section name does not accept special characters, and must have 3 to 8 characters';

}