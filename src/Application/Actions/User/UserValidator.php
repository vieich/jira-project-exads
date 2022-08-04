<?php

namespace App\Application\Actions\User;

use App\Domain\User\Exception\UserPasswordFormatException;
use App\Domain\User\Exception\UserPayloadStructureException;
use App\Domain\User\Exception\UserRoleException;
use App\Domain\User\Exception\UserUsernameFormatException;

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
            throw new UserUsernameFormatException();
        }
    }

    public function checkIfPasswordFormatIsValid(string $password): void
    {
        if (!preg_match("/^(?=.*?\d)[0-9a-zA-Z]{8,}$/", $password)) {
            throw new UserPasswordFormatException();
        }
    }

    public function checkIfPasswordAndCPasswordMatch(string $password, string $cpassword): void
    {
        if ($password !== $cpassword) {
            throw new UserPasswordFormatException('Password and confirm_password fields doenst match.');
        }
    }

    public function checkIfRoleIsValid(string $userRole): void
    {
        $userRoleLowerCase = strtolower($userRole);

        if ($userRoleLowerCase != 'client' && $userRoleLowerCase != 'admin') {
            throw new UserRoleException();
        }
    }
}
