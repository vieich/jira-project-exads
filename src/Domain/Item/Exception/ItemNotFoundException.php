<?php

namespace App\Domain\Item\Exception;

use App\Domain\DomainException\DomainRecordNotFoundException;

class ItemNotFoundException extends DomainRecordNotFoundException
{
    public $message = "Item not found.";
}