<?php

namespace App\Domain\Tab\Exception;

use App\Domain\DomainException\DomainRecordNotFoundException;

class TabNoParentTicketException extends DomainRecordNotFoundException
{
    public $message = 'The ticket does not exist, impossible to create Tab';
}