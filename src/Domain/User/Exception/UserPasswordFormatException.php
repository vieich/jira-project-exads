<?php

namespace App\Domain\User\Exception;

use App\Domain\DomainException\DomainDataFormatException;

class UserPasswordFormatException extends DomainDataFormatException
{
    public $message = "Password format is not valid, must have at least 8 characters and one must be a number.";
}