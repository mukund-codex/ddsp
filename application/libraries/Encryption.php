<?php

/**
 * Password Encryption Library
 */
class Encryption
{
    private static $options = [];

    private static $algo = PASSWORD_DEFAULT;

    private static $hash = null;

    private static $cost = 10;

    function __construct()
    {
        self::$options = [
            'cost' => self::$cost
        ];
    }   

    public static function encryptPassword(string $password)
    {
        return password_hash($password, self::$algo, self::$options);
    }

    public static function verifyPassword(string $password, string $hash)
    {
        return (true === password_verify($password, $hash)) ?? false;
    }

    public static function reHashPassword(string $password, string $oldHash)
    {
        if (true === password_verify($password, $oldHash)) {
            if (true === password_needs_rehash($oldHash, self::$algo, self::$options)) {
                return password_hash($password, self::$algo, self::$options);
            }
        }

        return null;
    }

    public static function getPasswordInfo(string $hash)
    {
        return password_get_info($hash);
    }
}

?>