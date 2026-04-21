<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:20px;">
    <h2 style="margin:0;">Publicaciones</h2>

    <a class="btn" href="/foro-universitario-php/public/posts/create">
        Nueva publicación
    </a>
</div>

<?php if (empty($posts)): ?>

    <div class="card">
        Aún no hay publicaciones.
    </div>

<?php else: ?>

    <?php foreach ($posts as $p): ?>

        <div class="card">
            <h3 style="margin-top:0;">
                <a
                    href="/foro-universitario-php/public/posts/show?id=<?= $p['id'] ?>"
                    style="text-decoration:none; color:#111827;"
                >
                    <?= htmlspecialchars($p['titulo']) ?>
                </a>
            </h3>

            <p>
                <?= nl2br(htmlspecialchars($p['contenido'])) ?>
            </p>

            <div style="color:#6b7280; font-size:14px; margin-top:15px;">
                Por <?= htmlspecialchars($p['nombre']) ?> |
                <?= htmlspecialchars($p['created_at']) ?>
            </div>
        </div>

    <?php endforeach; ?>

<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>