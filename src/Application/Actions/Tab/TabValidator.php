<?php

namespace App\Application\Actions\Tab;

use App\Domain\Tab\Exception\TabNameFormatException;
use App\Domain\Tab\Exception\TabNoAuthorizationException;
use App\Domain\Tab\Exception\TabPayloadStructureException;

class TabValidator
{
    private static $instance;

    public static function getInstance(): TabValidator
    {
        if(!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function checkIfHeaderIsMissing(string $header): void
    {
        if($header == "") {
            throw new TabNoAuthorizationException('Auth-Token is missing on the header.');
        }
    }

    public function checkIfPayloadFormatIsValid(array $args): void
    {
        foreach ($args as $key => $value) {
            if (!isset($value) || $value == "") {
                throw new TabPayloadStructureException('Payload is not valid, is missing the ' . $key . ' field');
            }
        }
    }

    public function checkIfTabNameIsValid($tabName): void
    {
        if (!preg_match("/^[A-Za-z]{2,8}$/", $tabName)) {
            throw new TabNameFormatException();
        }
    }
}