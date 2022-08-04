<?php

namespace App\Domain\User\Exception;

use App\Domain\DomainException\DomainDataFormatException;

class UserUsernameFormatException extends DomainDataFormatException
{
    public $message = 'Username must have 4 to 12 characters, only letters,numbers and _ allowed';
}