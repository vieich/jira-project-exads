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

    public function validateUser(string $name, string $role, bool $isActive): array
    {
        $response = [
            'message' => '',
            'hasSuccess' => true
        ];

        $sanitizedName = filter_Var($name, FILTER_SANITIZE_STRING);
        $sanitizedRole = filter_var($role, FILTER_SANITIZE_STRING);

        if (!$sanitizedName || strlen($sanitizedName) > 10) {
            $response['message'] = 'Failed on Name verification, max 10 characters';
            $response['hasSuccess'] = false;
            return $response;
        }

        $sanitizedRoleLowerCase = strtolower($sanitizedRole);

        if (!$sanitizedRole || ($sanitizedRoleLowerCase != 'client' && $sanitizedRoleLowerCase != 'admin')) {
            $response['message'] = 'Failed on Role verification, available roles client or admin ' . $sanitizedRole;
            $response['hasSuccess'] = false;
            return $response;
        }
        return $response;
    }

    public function createUniqueId(): string
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*_";

        return substr(str_shuffle($chars), 0, 8);
    }
}
