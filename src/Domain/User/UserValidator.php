<?php

namespace App\Domain\User;

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

    public function validateUser(string $name = null, string $role = null, string $token = null): array
    {
        $response = [
            'message' => '',
            'hasSuccess' => true
        ];

        if (isset($name)) {
            if (!$this->validateUserName($name)) {
                $response['message'] = 'Failed on Name verification, must have minimum 3 characters and max 12, Only A,a,numbers and _ allowed';
                $response['hasSuccess'] = false;
                return $response;
            }
        }

        if (isset($role)) {
            if (!$this->validateUserRole($role)) {
                $response['message'] = 'Failed on Role verification, available roles client or admin ';
                $response['hasSuccess'] = false;
            }
        }

        if (isset($token)) {
            if (!$this->validateUserToken($token)) {
                $response['message'] = 'Failed on Token verification, only A,a,numbers are allowed, and must have 8 characters';
                $response['hasSuccess'] = false;
            }
        }
        return $response;
    }

    private function validateUserRole(string $userRole): bool
    {
        $userRoleLowerCase = strtolower($userRole);

        return $userRoleLowerCase == 'client' || $userRoleLowerCase == 'admin';
    }

    private function validateUserName(string $userName): bool
    {
        /*
         * No white spaces
         * Only A,a,numbers and _ allowed
         * Must have between 6 and 12 characters
         */
        return preg_match("/^[A-Za-z]\\w{2,11}$/", $userName);
    }

    private function validateUserToken(string $userToken): bool
    {
        /*
         * Only A,a and numbers allowed, must have 8 characters
         */
        return preg_match("/^\w{8}$/", $userToken);
    }

    public function createUniqueId(): string
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

        return substr(str_shuffle($chars), 0, 8);
    }
}
