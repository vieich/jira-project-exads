<?php

namespace App\Application\Actions;

use App\Domain\DomainException\DomainPayloadStructureValidatorException;
use App\Domain\DomainException\DomainRecordWithoutAuthorizationException;

class Validator
{
    public function checkIfPayloadStructureIsValid(array $args): void
    {
        foreach ($args as $key => $value) {
            if (!isset($value) || $value == "") {
                throw new DomainPayloadStructureValidatorException('Payload is not valid, is missing the ' . $key . ' field');
            }
        }
    }

    public function checkIfHeaderIsMissing(string $header): void
    {
        if ($header == "") {
            throw new DomainRecordWithoutAuthorizationException('Auth-Token is missing on the header.');
        }
    }
}