<?php

require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/Auth.php';

class CommentController
{
    // 🔒 Verificar si usuario está baneado
    private function checkBanned()
    {
        $user = (new User())->getById(Auth::id());

        if ($user && !empty($user['is_banned'])) {
            setFlash('error', 'Tu cuenta está bloqueada.');
            header("Location: /foro-universitario-php/public/posts");
            exit;
        }
    }

    public function create()
    {
        Auth::requireAuth();
        $this->checkBanned();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $contenido = trim($_POST['contenido'] ?? '');
            $post_id   = (int) ($_POST['post_id'] ?? 0);

            if ($contenido === '' || $post_id <= 0) {
                setFlash('error', 'Datos inválidos.');
                header("Location: /foro-universitario-php/public/posts/show?id=" . $post_id);
                exit;
            }

            (new Comment())->create(
                $contenido,
                $post_id,
                Auth::id()
            );

            setFlash('success', 'Comentario agregado correctamente.');
            header("Location: /foro-universitario-php/public/posts/show?id=" . $post_id);
            exit;
        }

        header("Location: /foro-universitario-php/public/posts");
        exit;
    }

    public function delete()
    {
        Auth::requireAuth();
        $this->checkBanned();
        csrf_verify();

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

        Auth::requireOwnerOrAdmin($comment['user_id']);

        $commentModel->delete($id);

        setFlash('success', 'Comentario eliminado.');
        header("Location: /foro-universitario-php/public/posts/show?id=" . $post_id);
        exit;
    }

    public function edit($id)
    {
        Auth::requireAuth();
        $this->checkBanned();

        $commentModel = new Comment();
        $comment = $commentModel->getById((int)$id);

        if (!$comment) {
            setFlash('error', 'Comentario no encontrado.');
            header("Location: /foro-universitario-php/public/posts");
            exit;
        }

        Auth::requireOwnerOrAdmin($comment['user_id']);

        require_once __DIR__ . '/../views/comments/edit.php';
    }

    public function update()
    {
        Auth::requireAuth();
        $this->checkBanned();
        csrf_verify();

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

        Auth::requireOwnerOrAdmin($comment['user_id']);

        $commentModel->update($id, $contenido);

        setFlash('success', 'Comentario actualizado.');

        header("Location: /foro-universitario-php/public/posts/show?id=" . $comment['post_id']);
        exit;
    }
}