<?php
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../models/Comment.php';

class PostController
{

    public function create()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $titulo    = trim($_POST['titulo']);
            if (strlen($titulo) < 5 || strlen($titulo) > 255) {
                die("Título inválido");
            }
            $contenido = trim($_POST['contenido']);
            $categoria = $_POST['categoria'];
            $archivo   = null;

            // === PASO 3: SUBIDA DE ARCHIVOS SEGURA ===
            if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {

                // MIME permitidos
                $allowedTypes = [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                ];

                // Extensiones permitidas
                $allowedExtensions = ['pdf', 'doc', 'docx'];

                // Validar MIME real
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime  = finfo_file($finfo, $_FILES['archivo']['tmp_name']);
                finfo_close($finfo);

                if (!in_array($mime, $allowedTypes)) {
                    die("Tipo de archivo no permitido");
                }

                // Validar extensión
                $extension = strtolower(pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION));

                if (!in_array($extension, $allowedExtensions)) {
                    die("Extensión de archivo no permitida");
                }

                // Limitar tamaño (2 MB)
                $maxSize = 2 * 1024 * 1024;

                if ($_FILES['archivo']['size'] > $maxSize) {
                    die("Archivo demasiado grande");
                }

                // Generar nombre seguro
                $nombreArchivo = bin2hex(random_bytes(16)) . '.' . $extension;

                // Ruta destino
                $destino = __DIR__ . '/../../uploads/' . $nombreArchivo;

                if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $destino)) {
                    die("Error al subir el archivo");
                }

                $archivo = $nombreArchivo;
            }

            $post = new Post();
            $post->create($titulo, $contenido, $categoria, $archivo, $_SESSION['user_id']);

            header("Location: /posts");
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
        $postData = $post->getById($id);

        $comment = new Comment();
        $comments = $comment->getByPostId($id);

        require_once __DIR__ . '/../views/posts/show.php';
    }
}
