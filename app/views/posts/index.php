<h2>Publicaciones</h2>

<a href="/posts/create">Crear nueva publicación</a><br><br>

<?php foreach ($posts as $p): ?>
    <h3><a href="/posts/show?id=<?= $p['id'] ?>"><?= htmlspecialchars($p['titulo']) ?></a></h3>
    <p><?= htmlspecialchars($p['contenido']) ?></p>
    <small>Por <?= htmlspecialchars($p['nombre']) ?> | <?= $p['created_at'] ?></small>
    <hr>
<?php endforeach; ?>
