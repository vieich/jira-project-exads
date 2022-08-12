<?php

namespace App\Application\Actions;

use App\Domain\DomainException\DomainDataFormatException;
use App\Domain\DomainException\DomainPayloadStructureValidatorException;
use App\Domain\DomainException\DomainRecordWithoutAuthorizationException;

class Validator
{
    public function checkIfPayloadStructureIsValid(array $args): void
    {
        foreach ($args as $key => $value) {
            if (!isset($value) || $value == "") {
                throw new DomainPayloadStructureValidatorException('Payload is not valid, is missing the ' . $key . ' field, or its empty');
            }
        }
    }

    public function checkIfHeaderIsMissing(string $header): void
    {
        if ($header == "") {
            throw new DomainRecordWithoutAuthorizationException('Auth-Token is missing on the header.');
        }
    }

    public function checkIfShowHistoryIsValid($showHistoric): void
    {
        if (!is_bool($showHistoric)) {
            throw new DomainDataFormatException('Field showHistoric must be true or false');
        }
    }
}