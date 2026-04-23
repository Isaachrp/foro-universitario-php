<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<a class="btn secondary" href="/foro-universitario-php/public/posts">
    ← Volver
</a>

<div class="card">
    <h2><?= htmlspecialchars($user['nombre']) ?></h2>

    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>

    <p class="meta">
        Miembro desde: <?= htmlspecialchars($user['created_at']) ?>
    </p>

    <?php if (Auth::id() === (int)$user['id']): ?>
        <p style="color:#2563eb; font-weight:bold;">
            Este es tu perfil 👤
        </p>
    <?php endif; ?>
</div>

<!-- 🧵 POSTS -->
<div class="card">
    <h3>Publicaciones</h3>

    <?php if (empty($posts)): ?>
        <p style="color:#6b7280;">Este usuario no ha publicado nada.</p>
    <?php else: ?>

        <?php foreach ($posts as $p): ?>
            <div style="margin-bottom:15px;">
                <a href="/foro-universitario-php/public/posts/show?id=<?= $p['id'] ?>">
                    <strong><?= htmlspecialchars($p['titulo']) ?></strong>
                </a>

                <div class="meta">
                    <?= htmlspecialchars($p['created_at']) ?>
                </div>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>
</div>

<!-- 💬 COMMENTS -->
<div class="card">
    <h3>Comentarios</h3>

    <?php if (empty($comments)): ?>
        <p style="color:#6b7280;">Este usuario no ha comentado.</p>
    <?php else: ?>

        <?php foreach ($comments as $c): ?>
            <div style="margin-bottom:15px;">
                <strong>En:</strong>
                <a href="/foro-universitario-php/public/posts/show?id=<?= $c['post_id'] ?>">
                    <?= htmlspecialchars($c['titulo']) ?>
                </a>

                <p><?= nl2br(htmlspecialchars($c['contenido'])) ?></p>

                <div class="meta">
                    <?= htmlspecialchars($c['created_at']) ?>
                </div>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>