<?php

namespace App\Domain\Item\Exception;

use App\Domain\DomainException\DomainRecordNotFoundException;

class ItemNoParentSectionException extends DomainRecordNotFoundException
{

}