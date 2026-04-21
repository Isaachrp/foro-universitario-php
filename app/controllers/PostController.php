<?php

require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../models/Comment.php';

class PostController
{
    public function create()
    {
        if (!isset($_SESSION['user_id'])) {
            setFlash('warning', 'Debes iniciar sesión.');
            header("Location: /foro-universitario-php/public/login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $titulo    = trim($_POST['titulo'] ?? '');
            $contenido = trim($_POST['contenido'] ?? '');
            $categoria = trim($_POST['categoria'] ?? '');
            $archivo   = null;

            if ($titulo === '' || $contenido === '' || $categoria === '') {
                setFlash('error', 'Todos los campos obligatorios deben completarse.');
                header("Location: /foro-universitario-php/public/posts/create");
                exit;
            }

            if (
                isset($_FILES['archivo']) &&
                $_FILES['archivo']['error'] === 0
            ) {
                $nombreArchivo = time() . '_' . basename($_FILES['archivo']['name']);
                $destino = __DIR__ . '/../../uploads/' . $nombreArchivo;

                if (move_uploaded_file($_FILES['archivo']['tmp_name'], $destino)) {
                    $archivo = $nombreArchivo;
                }
            }

            $post = new Post();

            $post->create(
                $titulo,
                $contenido,
                $categoria,
                $archivo,
                (int) $_SESSION['user_id']
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
}