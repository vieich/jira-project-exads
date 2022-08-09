<?php

namespace App\Domain\Section\Exception;

use App\Domain\DomainException\DomainRecordWithoutAuthorizationException;

class SectionNoAuthorizationException extends DomainRecordWithoutAuthorizationException
{

}