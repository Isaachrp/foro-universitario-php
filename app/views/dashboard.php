<?php
//session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit;
}
?>

<h1>Bienvenido, <?= e($_SESSION['user_name']) ?>!</h1>
<p>Tu rol: <?= e($_SESSION['user_role']) ?></p>
<a href="/posts">Ver Publicaciones</a> | 
<a href="/posts/create">Crear Publicación</a> | 
<a href="/logout">Cerrar Sesión</a>

