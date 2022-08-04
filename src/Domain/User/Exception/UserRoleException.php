<?php

namespace App\Domain\User\Exception;

use App\Domain\DomainException\DomainDataFormatException;

class UserRoleException extends DomainDataFormatException
{
    public $message = 'Role incorrect, available roles: admin and client.';
}