<?php

namespace App\Domain\Permission\Exception;

use App\Domain\DomainException\DomainPayloadDataValidatorException;

class PermissionRoleException extends DomainPayloadDataValidatorException
{
    public $message = 'The role is incorrect, available roles: admin and client.';
}