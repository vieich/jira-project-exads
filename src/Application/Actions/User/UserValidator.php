<?php

namespace App\Application\Actions\User;

use App\Domain\User\Exception\UserPayloadDataException;
use App\Domain\User\Exception\UserPayloadStructureException;

class UserValidator
{
    private static $instance;

    public static function getInstance(): UserValidator
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function checkIfPayloadIsValid(array $args): void
    {
        foreach ($args as $key => $value) {
            if (!isset($value) || $value == "") {
                throw new UserPayloadStructureException('Payload is not valid, is missing the ' . $key . ' field, or its empty');
            }
        }
    }

    public function checkIfUsernameIsValid(string $name): void
    {
        /*
         * No white spaces
         * Only A,a,numbers and _ allowed
         * Must have between 3 and 12 characters
         */
        if (!preg_match("/^[A-Za-z]\\w{2,11}$/", $name)) {
            throw new UserPayloadDataException('Username must have at least 4 to 12 characters, only letters,numbers and _ allowed');
        }
    }

    public function isRoleValid(string $userRole): bool
    {
        $userRoleLowerCase = strtolower($userRole);

        return $userRoleLowerCase == 'client' || $userRoleLowerCase == 'admin';
    }

    public function isPasswordValid(string $password): bool
    {
        return preg_match("/^(?=.*?\d)[0-9a-zA-Z]{8,}$/", $password);
    }

    public function doesPasswordMatch(string $password, string $cpassword):bool
    {
        return $password == $cpassword;
    }
}
