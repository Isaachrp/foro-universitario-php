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

       <div style="margin-top:10px;">
            <form method="POST"
                action="/foro-universitario-php/public/posts/delete"
                style="margin-top:15px;"
                onsubmit="return confirm('¿Eliminar esta publicación?');">

                <?= csrf_input(); ?>

                <input type="hidden" name="id" value="<?= $postData['id'] ?>">

                <button class="btn" type="submit" onclick="this.disabled=true; this.form.submit();">
                    Eliminar
                </button>
                  <?php if (Auth::check() && (Auth::isOwner($postData['user_id']) || Auth::isAdmin())): ?>

                        <a class="btn secondary"
                        href="/foro-universitario-php/public/posts/edit?id=<?= $postData['id'] ?>">
                            Editar
                        </a>

                    <?php endif; ?>
            </form>
       </div>
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
                <strong>
                    <a href="/foro-universitario-php/public/users/show?id=<?= $c['user_id'] ?>">
                        <?= htmlspecialchars($c['nombre']) ?>
                    </a>
                </strong>

                <?= nl2br(htmlspecialchars($c['contenido'])) ?>

               <?php if (Auth::isOwner($c['user_id']) || Auth::isAdmin()): ?>

                    <form method="POST"
                        action="/foro-universitario-php/public/comments/delete"
                        style="display:inline;"
                        onsubmit="return confirm('¿Eliminar este comentario?');">

                        <?= csrf_input(); ?>

                        <input type="hidden" name="id" value="<?= $c['id'] ?>">
                        <input type="hidden" name="post_id" value="<?= $postData['id'] ?>">

                        <button class="btn secondary" type="submit">
                            Eliminar
                        </button>
                        <?php if (Auth::check() && (Auth::isOwner($c['user_id']) || Auth::isAdmin())): ?>

                            <a class="btn secondary"
                            href="/foro-universitario-php/public/comments/edit?id=<?= $c['id'] ?>">
                                Editar
                            </a>

                        <?php endif; ?>
                    </form>

                <?php endif; ?>

              
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