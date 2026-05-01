<?php require_once __DIR__ . '/layouts/header.php'; ?>

<div class="card">
    <h1>
        Bienvenido, <?= htmlspecialchars($_SESSION['user_name']) ?> 👋
    </h1>

    <!-- <p style="color:#6b7280; margin-bottom:20px;">
        Rol:
        <strong><?= htmlspecialchars($_SESSION['user_role']) ?></strong>
    </p> -->

    <div style="display:flex; gap:12px; flex-wrap:wrap;">
        <a class="btn" href="/foro-universitario-php/public/posts">
            Ver publicaciones
        </a>

        <a class="btn" href="/foro-universitario-php/public/posts/create">
            Crear publicación
        </a>

        <a class="btn secondary" href="/foro-universitario-php/public/logout">
            Cerrar sesión
        </a>
    </div>
</div>

<?php if (Auth::isAdmin()): ?>

<div class="card">
    <h3>Administrar usuarios</h3>

    <?php foreach ($users as $u): ?>

        <div style="margin-bottom:10px;">
            <strong><?= htmlspecialchars($u['nombre']) ?></strong>
            (<?= htmlspecialchars($u['email']) ?>)

            <?php if (!$u['is_banned']): ?>
                <form method="POST" action="/foro-universitario-php/public/admin/ban">
                    <?= csrf_input(); ?>
                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                    <input type="text" name="reason" placeholder="Motivo (opcional)">
                    <button class="btn">Banear</button>
                </form>
            <?php else: ?>
                <form method="POST" action="/foro-universitario-php/public/admin/unban">
                    <?= csrf_input(); ?>
                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                    <button class="btn secondary">Desbanear</button>
                </form>
            <?php endif; ?>
        </div>

    <?php endforeach; ?>
</div>

<?php endif; ?>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>