<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<a class="btn secondary" href="/foro-universitario-php/public/posts">
    ← Volver
</a>

<div class="card">
    <h2><?= htmlspecialchars($postData['titulo']) ?></h2>

    <p>
        <?= nl2br(htmlspecialchars($postData['contenido'])) ?>
    </p>

    <?php if (!empty($postData['archivo'])): ?>
        <p>
            Archivo:
            <a
                href="/foro-universitario-php/public/uploads/<?= urlencode($postData['archivo']) ?>"
                target="_blank"
            >
                <?= htmlspecialchars($postData['archivo']) ?>
            </a>
        </p>
    <?php endif; ?>
    <?php if (Auth::isOwner($postData['user_id']) || Auth::isAdmin()): ?>

        <form method="POST" action="/foro-universitario-php/public/posts/delete" style="margin-top:15px;">
            
            <?= csrf_input(); ?>
            
            <input type="hidden" name="id" value="<?= $postData['id'] ?>">
            
            <button class="btn" onclick="return confirm('¿Eliminar esta publicación?')">
                Eliminar
            </button>
        </form>

    <?php endif; ?>
</div>

<div class="card">
    <h3>Comentarios</h3>

    <?php if (empty($comments)): ?>

        <p style="color:#6b7280;">
            Aún no hay comentarios.
        </p>

    <?php else: ?>

        <?php foreach ($comments as $c): ?>

            <div style="padding:12px 0; border-bottom:1px solid #eee;">
                <strong><?= htmlspecialchars($c['nombre']) ?>:</strong><br>

                <?= nl2br(htmlspecialchars($c['contenido'])) ?>
            </div>

        <?php endforeach; ?>

    <?php endif; ?>
</div>

<div class="card">
    <h3>Agregar comentario</h3>

    <form method="POST" action="/foro-universitario-php/public/comments/create">

        <?= csrf_input(); ?>

        <input type="hidden" name="post_id" value="<?= $postData['id'] ?>">

        <textarea name="contenido" required></textarea>

        <button class="btn" type="submit">
            Comentar
        </button>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>