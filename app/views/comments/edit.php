<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card" style="max-width:600px; margin:auto;">
    <h2>Editar comentario</h2>

    <form method="POST"
          action="/foro-universitario-php/public/comments/update">

        <?= csrf_input(); ?>

        <input type="hidden" name="id" value="<?= $comment['id'] ?>">

        <textarea name="contenido" required>
<?= htmlspecialchars($comment['contenido']) ?>
        </textarea>

        <button class="btn" type="submit">Actualizar</button>

        <a class="btn secondary"
           href="/foro-universitario-php/public/posts/show?id=<?= $comment['post_id'] ?>">
           Cancelar
        </a>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>