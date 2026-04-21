<?php

require_once __DIR__ . '/../models/Comment.php';

class CommentController
{
    public function create()
    {
        if (!isset($_SESSION['user_id'])) {
            setFlash('warning', 'Debes iniciar sesión para comentar.');
            header("Location: /foro-universitario-php/public/login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $contenido = trim($_POST['contenido'] ?? '');
            $post_id   = (int) ($_POST['post_id'] ?? 0);
            $user_id   = (int) $_SESSION['user_id'];

            if ($contenido === '' || $post_id <= 0) {
                setFlash('error', 'Datos inválidos.');
                header("Location: /foro-universitario-php/public/posts/show?id=" . $post_id);
                exit;
            }

            $comment = new Comment();

            $comment->create(
                $contenido,
                $post_id,
                $user_id
            );

            setFlash('success', 'Comentario agregado correctamente.');
            header("Location: /foro-universitario-php/public/posts/show?id=" . $post_id);
            exit;
        }

        header("Location: /foro-universitario-php/public/posts");
        exit;
    }
}