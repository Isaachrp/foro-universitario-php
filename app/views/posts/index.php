<h2>Publicaciones</h2>

<a href="/posts/create">Crear nueva publicación</a><br><br>

<?php foreach ($posts as $p): ?>
    <h3><a href="/posts/show?id=<?= $p['id'] ?>"><?= e($p['titulo']) ?></a></h3>
    <p><?= e($p['contenido']) ?></p>
    <small>Por <?= e($p['nombre']) ?> | <?= $p['created_at'] ?></small>
    <hr>
<?php endforeach; ?>
<!-- ------------------------pruebas --------------------->
<pre>
SESSION USER ID: <?= var_dump($_SESSION['user_id']) ?>
SESSION USER ROLE: <?= var_dump($_SESSION['user_role']) ?>
</pre>
<!-- ------------------------------------------------- -->

