<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card" style="max-width:700px; margin:auto;">
    <h2>Nueva publicación</h2>

    <form
        method="POST"
        action="/foro-universitario-php/public/posts/create"
        enctype="multipart/form-data"
    >

        <?= csrf_input(); ?>

        <label>Título</label>
        <input
            type="text"
            name="titulo"
            maxlength="255"
            required
        >

        <label>Contenido</label>
        <textarea
            name="contenido"
            required
        ></textarea>

        <label>Categoría</label>
        <select name="categoria" required>
            <option value="academico">Académico</option>
            <option value="no_academico">No Académico</option>
        </select>

        <label>Archivo (opcional)</label>
        <input type="file" name="archivo">

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <button class="btn" type="submit">
                Publicar
            </button>

            <a class="btn secondary"
               href="/foro-universitario-php/public/posts">
               Cancelar
            </a>
        </div>

    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>