<h2>Crear Publicación</h2>

<form method="POST" action="/posts/create" enctype="multipart/form-data">
    <input type="text" name="titulo" placeholder="Título" required><br><br>
    <textarea name="contenido" placeholder="Contenido" required></textarea><br><br>
    <select name="categoria">
        <option value="academico">Académico</option>
        <option value="no_academico">No Académico</option>
    </select><br><br>
    <input type="file" name="archivo"><br><br>
    <button type="submit">Publicar</button>
</form>
