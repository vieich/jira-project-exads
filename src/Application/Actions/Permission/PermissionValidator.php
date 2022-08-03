<?php

namespace App\Application\Actions\Permission;

use App\Domain\Permission\Exception\PermissionAuthTokenException;
use App\Domain\Permission\Exception\PermissionPasswordFormatInvalid;
use App\Domain\Permission\Exception\PermissionRoleException;
use App\Infrastructure\Persistence\Permission\PermissionRepo;

class PermissionValidator
{
    private static $instance;

    public static function getInstance(): PermissionValidator
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function checkIfRoleIsValid(string $userRole): void
    {
        $userRoleLowerCase = strtolower($userRole);

        if($userRoleLowerCase != 'client' && $userRoleLowerCase != 'admin') {
            throw new PermissionRoleException();
        }
    }

    public function checkIfAuthTokenFormatIsValid(string $authToken): void
    {
        if (!ctype_xdigit($authToken) || strlen($authToken) != 64) {
            throw new PermissionAuthTokenException();
        }
    }

    public function checkIfPasswordFormatIsValid(string $password): void
    {
        if(!preg_match("/^(?=.*?\d)[0-9a-zA-Z]{8,}$/", $password)) {
            throw new PermissionPasswordFormatInvalid();
        }
    }

    public function checkIfPasswordAndCpassordMatch(string $password, string $cpassword): void
    {
        if($password !== $cpassword) {
            throw new PermissionPasswordFormatInvalid('Password and confirm_password fields doenst match.');
        }
    }
}
