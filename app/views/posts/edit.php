<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card" style="max-width:700px; margin:auto;">
    <h2>Editar publicación</h2>

    <form method="POST"
          action="/foro-universitario-php/public/posts/update"
          enctype="multipart/form-data">

        <?= csrf_input(); ?>

        <input type="hidden" name="id" value="<?= $post['id'] ?>">

        <label>Título</label>
        <input type="text" name="titulo" value="<?= htmlspecialchars($post['titulo']) ?>" required>

        <label>Contenido</label>
        <textarea name="contenido" required><?= htmlspecialchars($post['contenido']) ?></textarea>

        <label>Categoría</label>
        <select name="categoria">
            <option value="academico" <?= $post['categoria'] === 'academico' ? 'selected' : '' ?>>Académico</option>
            <option value="no_academico" <?= $post['categoria'] === 'no_academico' ? 'selected' : '' ?>>No Académico</option>
        </select>

        <label>Reemplazar archivo (opcional)</label>
        <input type="file" name="archivo">

        <button class="btn" type="submit">Actualizar</button>
        <a class="btn secondary" href="/foro-universitario-php/public/posts/show?id=<?= $post['id'] ?>">Cancelar</a>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>