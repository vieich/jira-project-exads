<?php

namespace App\Domain\User\Exception;

use App\Domain\DomainException\DomainDataFormatException;

class UserPasswordFormatException extends DomainDataFormatException
{
    public $message = "Password format is invalid, must have between 8 and 32 characters, and one must be a number.";
}