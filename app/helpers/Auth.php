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

    public static function user()
    {
        return [
            'id'   => $_SESSION['user_id'] ?? null,
            'name' => $_SESSION['user_name'] ?? null,
            'role' => $_SESSION['user_role'] ?? 'user'
        ];
    }

    public static function role()
    {
        return $_SESSION['user_role'] ?? 'user';
    }

    public static function isAdmin()
    {
        return self::role() === 'admin';
    }

    public static function requireAuth()
    {
        if (!self::check()) {
            header("Location: /foro-universitario-php/public/login");
            exit;
        }
    }

    public static function requireGuest()
    {
        if (self::check()) {
            header("Location: /foro-universitario-php/public/dashboard");
            exit;
        }
    }

    public static function isOwner($resourceUserId)
    {
        return self::id() === (int) $resourceUserId;
    }

    public static function requireOwnerOrAdmin($resourceUserId)
    {
        if (!self::check()) {
            header("Location: /foro-universitario-php/public/login");
            exit;
        }

        if (!self::isOwner($resourceUserId) && !self::isAdmin()) {
            http_response_code(403);
            exit('No tienes permiso para realizar esta acción.');
        }
    }
}