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

    public function delete()
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

        $id      = (int) ($_POST['id'] ?? 0);
        $post_id = (int) ($_POST['post_id'] ?? 0);

        if ($id <= 0) {
            setFlash('error', 'Comentario inválido.');
            header("Location: /foro-universitario-php/public/posts");
            exit;
        }

        $commentModel = new Comment();
        $comment = $commentModel->getById($id);

        if (!$comment) {
            setFlash('error', 'El comentario no existe.');
            header("Location: /foro-universitario-php/public/posts");
            exit;
        }

        // 🔒 PERMISO REAL
        if (!Auth::isOwner($comment['user_id']) && !Auth::isAdmin()) {
            setFlash('error', 'No tienes permisos.');
            header("Location: /foro-universitario-php/public/posts/show?id=" . $post_id);
            exit;
        }

        $commentModel->delete($id);

        setFlash('success', 'Comentario eliminado correctamente.');
        header("Location: /foro-universitario-php/public/posts/show?id=" . $post_id);
        exit;
    }

    public function edit($id)
    {
        if (!Auth::check()) {
            setFlash('warning', 'Debes iniciar sesión.');
            header("Location: /foro-universitario-php/public/login");
            exit;
        }

        $commentModel = new Comment();
        $comment = $commentModel->getById((int)$id);

        if (!$comment) {
            setFlash('error', 'Comentario no encontrado.');
            header("Location: /foro-universitario-php/public/posts");
            exit;
        }

        if (!Auth::isOwner($comment['user_id']) && !Auth::isAdmin()) {
            setFlash('error', 'No tienes permisos.');
            header("Location: /foro-universitario-php/public/posts");
            exit;
        }

        require_once __DIR__ . '/../views/comments/edit.php';
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
        $contenido = trim($_POST['contenido'] ?? '');

        if ($id <= 0 || $contenido === '') {
            setFlash('error', 'Datos inválidos.');
            header("Location: /foro-universitario-php/public/posts");
            exit;
        }

        $commentModel = new Comment();
        $comment = $commentModel->getById($id);

        if (!$comment) {
            setFlash('error', 'Comentario no existe.');
            header("Location: /foro-universitario-php/public/posts");
            exit;
        }

        if (!Auth::isOwner($comment['user_id']) && !Auth::isAdmin()) {
            setFlash('error', 'No tienes permisos.');
            header("Location: /foro-universitario-php/public/posts");
            exit;
        }

        $commentModel->update($id, $contenido);

        setFlash('success', 'Comentario actualizado.');

        header("Location: /foro-universitario-php/public/posts/show?id=" . $comment['post_id']);
        exit;
    }
}