<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Foro Universitario</title>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f4f6f8;
    color: #111827;
}

nav {
    background: #111827;
    color: white;
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}

nav .brand {
    font-size: 20px;
    font-weight: bold;
}

nav a {
    color: white;
    text-decoration: none;
    margin-left: 14px;
}

.container {
    max-width: 1000px;
    margin: auto;
    padding: 30px 20px;
}

.card {
    background: white;
    border-radius: 14px;
    padding: 24px;
    box-shadow: 0 8px 20px rgba(0,0,0,.06);
    margin-bottom: 20px;
}

.btn {
    display: inline-block;
    padding: 10px 16px;
    border-radius: 8px;
    text-decoration: none;
    background: #2563eb;
    color: white;
    border: none;
    cursor: pointer;
}

.btn.secondary {
    background: #6b7280;
}

input, textarea, select {
    width: 100%;
    padding: 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    margin-top: 6px;
    margin-bottom: 16px;
}

textarea {
    min-height: 120px;
    resize: vertical;
}
</style>
</head>
<body>

<nav>
    <div class="brand">🎓 Foro Universitario</div>

    <div>
        <?php if (Auth::check()): ?>
            <a href="/foro-universitario-php/public/dashboard">Dashboard</a>
            <a href="/foro-universitario-php/public/posts">Publicaciones</a>
            <a href="/foro-universitario-php/public/logout">Salir</a>
        <?php else: ?>
            <a href="/foro-universitario-php/public/login">Login</a>
            <a href="/foro-universitario-php/public/register">Registro</a>
        <?php endif; ?>
    </div>
</nav>

<div class="container">

<?php if ($flash = getFlash()): ?>

    <?php
        $bg = '#dbeafe';
        $color = '#1e3a8a';

        if ($flash['type'] === 'success') {
            $bg = '#dcfce7';
            $color = '#166534';
        }

        if ($flash['type'] === 'error') {
            $bg = '#fee2e2';
            $color = '#991b1b';
        }

        if ($flash['type'] === 'warning') {
            $bg = '#fef3c7';
            $color = '#92400e';
        }
    ?>

    <div style="
        background: <?= $bg ?>;
        color: <?= $color ?>;
        padding: 14px 18px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-weight: bold;
    ">
        <?= htmlspecialchars($flash['message']) ?>
    </div>

<?php endif; ?>