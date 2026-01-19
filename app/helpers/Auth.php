<?php

class Auth
{
    public static function check()
    {
        return isset($_SESSION['user_id']);
    }

    public static function id()
    {
        return $_SESSION['user_id'] ?? null;
    }

    public static function role()
    {
        return $_SESSION['user_role'] ?? 'estudiante';
    }

    public static function isAdmin()
    {
        return self::role() === 'admin';
    }
}
