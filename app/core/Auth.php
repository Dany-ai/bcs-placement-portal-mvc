<?php

class Auth
{
    public static function user(): ?array
    {
        Session::init();
        $u = Session::get('user');
        return is_array($u) ? $u : null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function role(): ?string
    {
        $user = self::user();
        return $user['role'] ?? null;
    }

    /**
     * Require a specific role (string) OR any role in array.
     * Examples:
     *   Auth::requireRole('student');
     *   Auth::requireRole(['admin','employer']);
     */
    public static function requireRole(string|array $role): void
    {
        Session::init();

        if (!self::check()) {
            self::redirectToLogin();
        }

        $current = self::role();
        $allowed = is_array($role) ? $role : [$role];

        if (!in_array($current, $allowed, true)) {
            self::redirectToLogin();
        }
    }

    public static function login(array $user): void
    {
        Session::init();
        // Prevent session fixation
        Session::regenerate();

        Session::set('user', [
            'id'    => (int)($user['id'] ?? 0),
            'email' => (string)($user['email'] ?? ''),
            'role'  => (string)($user['role'] ?? ''),
            'name'  => (string)($user['name'] ?? ''),
        ]);
    }

    public static function logout(): void
    {
        Session::destroy();
    }

    private static function redirectToLogin(): void
    {
        header('Location: ' . URL_ROOT . '/auth/login');
        exit;
    }
}