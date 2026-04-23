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
        $post = new Post();
        $posts = $post->getAll();

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
}