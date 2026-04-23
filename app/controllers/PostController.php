<?php

require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../helpers/Auth.php';

class PostController
{
    public function create()
    {
        // 🔒 Requiere sesión
        Auth::requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $titulo    = trim($_POST['titulo'] ?? '');
            $contenido = trim($_POST['contenido'] ?? '');
            $categoria = trim($_POST['categoria'] ?? '');
            $archivo   = null;

            // 🔒 Validación
            if ($titulo === '' || $contenido === '' || $categoria === '') {
                setFlash('error', 'Todos los campos obligatorios deben completarse.');
                header("Location: /foro-universitario-php/public/posts/create");
                exit;
            }

            // 🔒 Manejo seguro de archivos
            if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === 0) {

                $fileTmp  = $_FILES['archivo']['tmp_name'];
                $fileSize = $_FILES['archivo']['size'];

                // Tamaño máximo
                if ($fileSize > 5 * 1024 * 1024) {
                    setFlash('error', 'El archivo es demasiado grande (máx 5MB).');
                    header("Location: /foro-universitario-php/public/posts/create");
                    exit;
                }

                // Extensión
                $extension = strtolower(pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];

                if (!in_array($extension, $allowedExtensions)) {
                    setFlash('error', 'Tipo de archivo no permitido.');
                    header("Location: /foro-universitario-php/public/posts/create");
                    exit;
                }

                $extension = $extension === 'jpeg' ? 'jpg' : $extension;

                // Nombre seguro
                $nombreSeguro = bin2hex(random_bytes(16)) . '.' . $extension;

                // Ruta uploads
                $uploadDir = __DIR__ . '/../../public/uploads/';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $destino = $uploadDir . $nombreSeguro;

                if (!move_uploaded_file($fileTmp, $destino)) {
                    setFlash('error', 'Error al subir el archivo.');
                    header("Location: /foro-universitario-php/public/posts/create");
                    exit;
                }

                $archivo = $nombreSeguro;
            }

            // Guardar en BD
            $post = new Post();

            $post->create(
                $titulo,
                $contenido,
                $categoria,
                $archivo,
                Auth::id() // 🔥 AQUÍ ESTÁ LA MEJORA
            );

            setFlash('success', 'Publicación creada correctamente.');
            header("Location: /foro-universitario-php/public/posts");
            exit;
        }

        require_once __DIR__ . '/../views/posts/create.php';
    }

    public function index()
    {
        $limit = 5;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $search = trim($_GET['search'] ?? '');
        $categoria = trim($_GET['categoria'] ?? '');

        $post = new Post();

        $posts = $post->searchPaginated($search, $categoria, $limit, $offset);
        $total = $post->countSearch($search, $categoria);

        $totalPages = ceil($total / $limit);

        require_once __DIR__ . '/../views/posts/index.php';
    }

    public function show($id)
    {
        $post = new Post();
        $postData = $post->getById((int) $id);

        if (!$postData) {
            setFlash('error', 'La publicación no existe.');
            header("Location: /foro-universitario-php/public/posts");
            exit;
        }

        $comment = new Comment();
        $comments = $comment->getByPostId((int) $id);

        require_once __DIR__ . '/../views/posts/show.php';
    }

    public function delete($id)
    {
        Auth::requireAuth();
        csrf_verify();
        $post = new Post();
        $postData = $post->getById((int)$id);

        if (!$postData) {
            setFlash('error', 'Post no encontrado.');
            header("Location: /foro-universitario-php/public/posts");
            exit;
        }

        // 🔒 AQUÍ ESTÁ LA MAGIA
        Auth::requireOwnerOrAdmin($postData['user_id']);

        $post->delete((int)$id);

        setFlash('success', 'Post eliminado.');
        header("Location: /foro-universitario-php/public/posts");
        exit;
    }

    public function edit($id)
    {
        if (!Auth::check()) {
            setFlash('warning', 'Debes iniciar sesión.');
            header("Location: /foro-universitario-php/public/login");
            exit;
        }

        $postModel = new Post();
        $post = $postModel->getById((int)$id);

        if (!$post) {
            setFlash('error', 'Publicación no encontrada.');
            header("Location: /foro-universitario-php/public/posts");
            exit;
        }

        if (!Auth::isOwner($post['user_id']) && !Auth::isAdmin()) {
            setFlash('error', 'No tienes permisos.');
            header("Location: /foro-universitario-php/public/posts");
            exit;
        }

        require_once __DIR__ . '/../views/posts/edit.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /foro-universitario-php/public/posts");
            exit;
        }

        csrf_verify();

        if (!Auth::check()) {
            setFlash('warning', 'Debes iniciar sesión.');
            header("Location: /foro-universitario-php/public/login");
            exit;
        }

        $id        = (int) ($_POST['id'] ?? 0);
        $titulo    = trim($_POST['titulo'] ?? '');
        $contenido = trim($_POST['contenido'] ?? '');
        $categoria = trim($_POST['categoria'] ?? '');

        if ($id <= 0 || $titulo === '' || $contenido === '' || $categoria === '') {
            setFlash('error', 'Datos inválidos.');
            header("Location: /foro-universitario-php/public/posts");
            exit;
        }

        $postModel = new Post();
        $post = $postModel->getById($id);

        if (!$post) {
            setFlash('error', 'Publicación no existe.');
            header("Location: /foro-universitario-php/public/posts");
            exit;
        }

        if (!Auth::isOwner($post['user_id']) && !Auth::isAdmin()) {
            setFlash('error', 'No tienes permisos.');
            header("Location: /foro-universitario-php/public/posts");
            exit;
        }

        $archivo = $post['archivo'];

        // 📎 Nuevo archivo (opcional)
        if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === 0) {

            $fileTmp  = $_FILES['archivo']['tmp_name'];
            $fileSize = $_FILES['archivo']['size'];

            if ($fileSize > 5 * 1024 * 1024) {
                setFlash('error', 'Archivo demasiado grande.');
                header("Location: /foro-universitario-php/public/posts/edit?id=" . $id);
                exit;
            }

            $extension = strtolower(pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'pdf'];

            if (!in_array($extension, $allowed)) {
                setFlash('error', 'Tipo de archivo no permitido.');
                header("Location: /foro-universitario-php/public/posts/edit?id=" . $id);
                exit;
            }

            $extension = $extension === 'jpeg' ? 'jpg' : $extension;
            $nombreSeguro = bin2hex(random_bytes(16)) . '.' . $extension;

            $uploadDir = __DIR__ . '/../../public/uploads/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $destino = $uploadDir . $nombreSeguro;

            if (!move_uploaded_file($fileTmp, $destino)) {
                setFlash('error', 'Error al subir archivo.');
                header("Location: /foro-universitario-php/public/posts/edit?id=" . $id);
                exit;
            }

            // 🧹 eliminar archivo anterior
            if (!empty($post['archivo'])) {
                $old = $uploadDir . $post['archivo'];
                if (file_exists($old)) {
                    unlink($old);
                }
            }

            $archivo = $nombreSeguro;
        }

        $postModel->update($id, $titulo, $contenido, $categoria, $archivo);

        setFlash('success', 'Publicación actualizada.');
        header("Location: /foro-universitario-php/public/posts/show?id=" . $id);
        exit;
    }
}