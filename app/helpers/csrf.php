<?php

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_input(): string
{
    $token = csrf_token();

    return '<input type="hidden" name="csrf_token" value="' .
        htmlspecialchars($token, ENT_QUOTES, 'UTF-8') .
        '">';
}

function csrf_verify(): void
{
    $session = $_SESSION['csrf_token'] ?? '';
    $posted  = $_POST['csrf_token'] ?? '';

    if (!$session || !$posted || !hash_equals($session, $posted)) {
        http_response_code(403);
        exit('Solicitud inválida (CSRF).');
    }
}