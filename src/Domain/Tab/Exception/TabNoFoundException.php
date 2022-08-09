<?php

namespace App\Domain\Tab\Exception;

use App\Domain\DomainException\DomainRecordNotFoundException;

class TabNoFoundException extends DomainRecordNotFoundException
{
    public $message = 'The tab does not exist.';
}