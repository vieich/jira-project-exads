<?php

namespace App\Domain\Section\Exception;

use App\Domain\DomainException\DomainRecordNotFoundException;

class SectionNoParentException extends DomainRecordNotFoundException
{
    public $message = 'The Tab does not exist, impossible to create Section';
}