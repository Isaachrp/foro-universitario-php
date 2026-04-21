<?php require_once __DIR__ . '/layouts/header.php'; ?>

<div class="card">
    <h1>
        Bienvenido, <?= htmlspecialchars($_SESSION['user_name']) ?> 👋
    </h1>

    <p style="color:#6b7280; margin-bottom:20px;">
        Rol:
        <strong><?= htmlspecialchars($_SESSION['user_role']) ?></strong>
    </p>

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

<?php require_once __DIR__ . '/layouts/footer.php'; ?>