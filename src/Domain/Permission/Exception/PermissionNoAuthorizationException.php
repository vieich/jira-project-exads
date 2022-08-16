<?php

namespace App\Domain\Permission\Exception;

use App\Domain\DomainException\DomainRecordWithoutAuthorizationException;

class PermissionNoAuthorizationException extends DomainRecordWithoutAuthorizationException
{

}