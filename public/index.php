<?php
require_once __DIR__ . '/../app/helpers/security.php';

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'httponly' => true,   // JS no puede leer la cookie
    'secure' => false,    // true SOLO si usas HTTPS
    'samesite' => 'Lax'
]);


session_start();
/*
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
*/

require_once __DIR__ . '/../routes/web.php';
