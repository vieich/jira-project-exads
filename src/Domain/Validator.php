<?php

namespace App\Domain;

use App\Domain\DomainException\DomainDataFormatException;
use App\Domain\DomainException\DomainPayloadStructureValidatorException;
use App\Domain\DomainException\DomainRecordWithoutAuthorizationException;

class Validator
{
    /**
     * @param array $args
     *
     * @return void
     *
     * @throws DomainPayloadStructureValidatorException
     */
    public function checkIfPayloadStructureIsValid(array $args): void
    {
        foreach ($args as $key => $value) {
            if (!isset($value) || $value == "") {
                throw new DomainPayloadStructureValidatorException(
                    'Payload is not valid, is missing the ' . $key . ' field, or its empty'
                );
            }
        }
    }


    /**
     * @throws DomainDataFormatException
     */
    public function checkIfShowDeletedIsValid($showHistoric): void
    {
        if (!is_bool($showHistoric)) {
            throw new DomainDataFormatException('Field showHistoric must be true or false');
        }
    }
}
