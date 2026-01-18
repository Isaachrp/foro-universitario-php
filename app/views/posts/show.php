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
