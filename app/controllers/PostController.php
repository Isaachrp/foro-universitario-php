<?php
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../models/Comment.php';

class PostController {

    public function create() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo = trim($_POST['titulo']);
            $contenido = trim($_POST['contenido']);
            $categoria = $_POST['categoria'];
            $archivo = null;

            // Manejo de archivo
            if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === 0) {
                $nombreArchivo = time() . '_' . $_FILES['archivo']['name'];
                $destino = __DIR__ . '/../../uploads/' . $nombreArchivo;
                move_uploaded_file($_FILES['archivo']['tmp_name'], $destino);
                $archivo = $nombreArchivo;
            }

            $post = new Post();
            $post->create($titulo, $contenido, $categoria, $archivo, $_SESSION['user_id']);

            header("Location: /posts");
            exit;
        }

        require_once __DIR__ . '/../views/posts/create.php';
    }

    public function index() {
        $post = new Post();
        $posts = $post->getAll();
        require_once __DIR__ . '/../views/posts/index.php';
    }

    public function show($id) {
        $post = new Post();
        $postData = $post->getById($id);

        $comment = new Comment();
        $comments = $comment->getByPostId($id);

        require_once __DIR__ . '/../views/posts/show.php';
    }
}
