<?php

namespace App\Domain\Item\Exception;

use App\Domain\DomainException\DomainRecordWithoutAuthorizationException;

class ItemNoAuthorizationException extends DomainRecordWithoutAuthorizationException
{

}