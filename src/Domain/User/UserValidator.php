<?php

namespace App\Domain\User;

use App\Domain\User\Exception\UserNoAuthorizationException;
use App\Domain\User\Exception\UserPasswordFormatException;
use App\Domain\User\Exception\UserRoleException;
use App\Domain\User\Exception\UserUsernameFormatException;
use App\Domain\Validator;

class UserValidator extends Validator
{
    /**
     * @throws UserUsernameFormatException
     */
    public function checkIfUsernameIsValid(string $name): void
    {
        /*
         * No white spaces
         * Only A,a,numbers and _ allowed
         * Must have between 3 and 12 characters
         */
        if (!preg_match("/^[A-Za-z]\\w{3,11}$/", $name)) {
            throw new UserUsernameFormatException();
        }
    }

    /**
     * @throws UserPasswordFormatException
     */
    public function checkIfPasswordFormatIsValid(string $password): void
    {
        if (!preg_match("/^(?=.*?\d)[0-9a-zA-Z]{8,32}$/", $password)) {
            throw new UserPasswordFormatException();
        }
    }

    /**
     * @throws UserPasswordFormatException
     */
    public function checkIfPasswordAndCPasswordMatch(string $password, string $cpassword): void
    {
        if ($password !== $cpassword) {
            throw new UserPasswordFormatException('Password and confirm_password fields does not match.');
        }
    }

    /**
     * @throws UserRoleException
     */
    public function checkIfRoleIsValid(string $userRole): void
    {
        $userRoleLowerCase = strtolower($userRole);

        if ($userRoleLowerCase != 'client' && $userRoleLowerCase != 'admin') {
            throw new UserRoleException();
        }
    }

    /**
     * @throws UserNoAuthorizationException
     */
    public function checkIfUserTokenMatchTheUserId(int $userId, int $userIdByToken): void
    {
        if ($userId != $userIdByToken) {
                throw new UserNoAuthorizationException('The user that you are trying to update is not yours.');
        }
    }

}
