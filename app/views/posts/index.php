<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:20px;">
    <h2 style="margin:0;">Publicaciones</h2>

    <a class="btn" href="/foro-universitario-php/public/posts/create">
        Nueva publicación
    </a>

        <form method="GET" style="margin-bottom:20px;">
        
            <input
                type="text"
                name="search"
                placeholder="Buscar publicaciones..."
                value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
            >

            <select name="categoria">
                <option value="">Todas las categorías</option>
                <option value="academico"
                    <?= ($_GET['categoria'] ?? '') === 'academico' ? 'selected' : '' ?>>
                    Académico
                </option>
                <option value="no_academico"
                    <?= ($_GET['categoria'] ?? '') === 'no_academico' ? 'selected' : '' ?>>
                    No Académico
                </option>
            </select>

            <button class="btn" type="submit">Buscar</button>
        </form>
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
                Por 
                <a href="/foro-universitario-php/public/users/show?id=<?= $p['user_id'] ?>">
                    <?= htmlspecialchars($p['nombre']) ?>
                </a>|
                <?= htmlspecialchars($p['created_at']) ?>
            </div>
        </div>

    <?php endforeach; ?>

<?php endif; ?>
<div style="margin-top:20px; display:flex; gap:6px; flex-wrap:wrap;">

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>

        <a
            class="btn <?= $i == $page ? '' : 'secondary' ?>"
            href="/foro-universitario-php/public/posts?
            page=<?= $i ?>
            &search=<?= urlencode($_GET['search'] ?? '') ?>
            &categoria=<?= urlencode($_GET['categoria'] ?? '') ?>"
        >
            <?= $i ?>
        </a>

    <?php endfor; ?>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>