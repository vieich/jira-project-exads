<?php

namespace App\Domain\Item\Exception;

use App\Domain\DomainException\DomainDataFormatException;

class ItemNameFormatException extends DomainDataFormatException
{
    public $message = 'Item name invalid, only letters, numbers and _ accepted, and must have 3 to 13 characters';
}