<?php

namespace App\Domain\Permission\Exception;

use App\Domain\DomainException\DomainPayloadDataValidatorException;

class PermissionPasswordFormatInvalid extends DomainPayloadDataValidatorException
{
    public $message = "Password format is not valid, must have at least 8 characters and one must be a number";
}