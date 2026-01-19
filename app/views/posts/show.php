<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    echo "Sesión NO iniciada";
} else {
    echo "Sesión iniciada";
}
?>

<h2><?= e($postData['titulo']) ?></h2>
<p><?= e($postData['contenido']) ?></p>

<?php if ($postData['archivo']): ?>
    <p>Archivo: <a href="/uploads/<?= $postData['archivo'] ?>" target="_blank"><?= $postData['archivo'] ?></a></p>
<?php endif; ?>

<hr>
<h3>Comentarios</h3>
<?php foreach ($comments as $c): ?>
    <p><strong><?= e($c['nombre']) ?>:</strong> <?= e($c['contenido']) ?></p>
<?php endforeach; ?>

<hr>
<h4>Agregar comentario</h4>
<form method="POST" action="/comments/create">
    <input type="hidden" name="post_id" value="<?= $postData['id'] ?>">
    <textarea name="contenido" required></textarea><br><br>
    <button type="submit">Comentar</button>
</form>
<!-- temporal para pruebas -->
<pre>
POST USER ID: <?= var_dump($postData['user_id']) ?>
SESSION USER ID: <?= var_dump(Auth::id()) ?>
ROLE: <?= var_dump(Auth::role()) ?>
</pre>
<?php if (Auth::check()): ?>
    <pre>
        SESSION USER ID: <?= Auth::id() ?>
    </pre>
<?php endif; ?>

<!-- --------------------------------------------------- -->
 <!-- ------------------------pruebas --------------------->
<pre>
SESSION USER ID: <?= var_dump($_SESSION['user_id']) ?>
SESSION USER ROLE: <?= var_dump($_SESSION['user_role']) ?>
</pre>
<!-- ------------------------------------------------- -->
<?php if ($postData['user_id'] === Auth::id() || Auth::isAdmin()): ?>
    <a href="/posts/delete?id=<?= $postData['id'] ?>"
        onclick="return confirm('¿Seguro que quieres borrar este post?')">
        Borrar
    </a>
<?php endif; ?>