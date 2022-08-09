<?php

namespace App\Domain\Tab\Exception;

use App\Domain\DomainException\DomainOperationException;

class TabOperationException extends DomainOperationException
{
    public $message = "Operation failed.";
}