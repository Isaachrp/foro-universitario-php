<?php

require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../helpers/Auth.php';

class CommentController
{
    public function create()
    {
        // 🔒 Requiere autenticación
        Auth::requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $contenido = trim($_POST['contenido'] ?? '');
            $post_id   = (int) ($_POST['post_id'] ?? 0);

            // 🔒 Validación
            if ($contenido === '' || $post_id <= 0) {
                setFlash('error', 'Datos inválidos.');
                header("Location: /foro-universitario-php/public/posts/show?id=" . $post_id);
                exit;
            }

            $comment = new Comment();

            $comment->create(
                $contenido,
                $post_id,
                Auth::id() // 🔥 ya no usamos $_SESSION
            );

            setFlash('success', 'Comentario agregado correctamente.');
            header("Location: /foro-universitario-php/public/posts/show?id=" . $post_id);
            exit;
        }

        // fallback
        header("Location: /foro-universitario-php/public/posts");
        exit;
    }
}