<?php

// 🔒 Configuración segura de sesión (ANTES de session_start)
ini_set('session.use_strict_mode', 1);

session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => '',
    'secure'   => false, // ⚠️ en producción true (HTTPS)
    'httponly' => true,
    'samesite' => 'Strict'
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🔒 Protección contra hijacking (SOLO User Agent)
if (!isset($_SESSION['user_agent'])) {
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
}

if ($_SESSION['user_agent'] !== ($_SERVER['HTTP_USER_AGENT'] ?? '')) {
    session_unset();
    session_destroy();
    exit('Sesión inválida.');
}

// 🔒 Expiración de sesión (30 min)
$timeout = 1800;

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    session_unset();
    session_destroy();
    header("Location: /foro-universitario-php/public/login");
    exit;
}

$_SESSION['last_activity'] = time();

// Helpers
require_once __DIR__ . '/../app/helpers/flash.php';
require_once __DIR__ . '/../app/helpers/csrf.php';
require_once __DIR__ . '/../app/helpers/rate_limit.php';
require_once __DIR__ . '/../app/helpers/Auth.php';
// Router
require_once __DIR__ . '/../routes/web.php';