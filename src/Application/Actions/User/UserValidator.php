<?php

namespace App\Application\Actions\User;

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

    public function isUsernameValid(string $name): bool
    {
        /*
         * No white spaces
         * Only A,a,numbers and _ allowed
         * Must have between 6 and 12 characters
         */
        return preg_match("/^[A-Za-z]\\w{2,11}$/", $name);
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