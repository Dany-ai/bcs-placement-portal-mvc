<?php
// General application configuration

if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

if (!defined('URL_ROOT')) {
    // adjust if you changed port / URL
    define('URL_ROOT', 'http://localhost/bcs-placement-portal/public');
}

if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'BCS Placement Portal');
}

/**
 * Default "from" email address used when the application
 * sends notification emails (e.g. for placement matches).
 *
 * You can change this to something that works on your
 * local setup or hosting environment.
 */
if (!defined('MAIL_FROM')) {
    define('MAIL_FROM', 'no-reply@bcs-placement-portal.local');
}
