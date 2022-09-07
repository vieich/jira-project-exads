<?php

namespace App\Domain;

use App\Domain\DomainException\DomainPayloadStructureValidatorException;

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


    public function transformShowDeletedIntoBoolean($showDeleted): bool
    {
        return $showDeleted === 'true';
    }

}

