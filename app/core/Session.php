<?php

class Session
{
    private static bool $started = false;

    public static function init(): void
    {
        if (self::$started) {
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            // Secure-ish defaults (best effort for local dev too)
            $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

            // Must be set BEFORE session_start
            ini_set('session.use_strict_mode', '1');
            ini_set('session.use_only_cookies', '1');

            // Try to reduce risk if someone deploys this
            $cookieParams = session_get_cookie_params();
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => $cookieParams['path'] ?? '/',
                'domain'   => $cookieParams['domain'] ?? '',
                'secure'   => $isHttps,   // true on HTTPS
                'httponly' => true,
                'samesite' => 'Lax',
            ]);

            session_start();
        }

        self::$started = true;

        // Basic CSRF token bootstrap (we'll wire into forms next commit)
        if (!isset($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
    }

    public static function regenerate(): void
    {
        self::init();
        session_regenerate_id(true);
    }

    public static function set(string $key, mixed $value): void
    {
        self::init();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::init();
        return $_SESSION[$key] ?? $default;
    }

    public static function remove(string $key): void
    {
        self::init();
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        self::init();

        $_SESSION = [];

        // Clear cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, [
                'path'     => $params['path'] ?? '/',
                'domain'   => $params['domain'] ?? '',
                'secure'   => $params['secure'] ?? false,
                'httponly' => $params['httponly'] ?? true,
                'samesite' => 'Lax',
            ]);
        }

        session_destroy();
        self::$started = false;
    }

    // ---- CSRF helpers (used next commit) ----

    public static function csrfToken(): string
    {
        self::init();
        return (string)$_SESSION['_csrf'];
    }

    public static function verifyCsrf(?string $token): bool
    {
        self::init();
        if (!$token) return false;
        return hash_equals((string)$_SESSION['_csrf'], (string)$token);
    }
}