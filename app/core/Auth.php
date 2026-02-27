<?php

class Auth
{
    public static function user()
    {
        return Session::get('user');
    }

    public static function check()
    {
        return self::user() !== null;
    }

    public static function role()
    {
        $user = self::user();
        return $user['role'] ?? null;
    }

    public static function requireRole($role)
    {
        if (!self::check() || self::role() !== $role) {
            header('Location: ' . URL_ROOT . '/auth/login');
            exit;
        }
    }
}
