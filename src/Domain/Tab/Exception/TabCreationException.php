<?php

namespace App\Domain\Tab\Exception;

use App\Domain\DomainException\DomainCreationException;

class TabCreationException extends DomainCreationException
{
    public $message = 'There was an error while creating the Tab.';
}