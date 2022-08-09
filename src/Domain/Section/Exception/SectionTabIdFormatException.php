<?php

namespace App\Domain\Section\Exception;

use App\Domain\DomainException\DomainPayloadDataValidatorException;

class SectionTabIdFormatException extends DomainPayloadDataValidatorException
{
    public $message = 'tab_id field must be an number';
}