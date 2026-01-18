<?php
require_once __DIR__ . '/../models/Comment.php';

class CommentController {
    public function create() {
        //session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $contenido = trim($_POST['contenido']);
            $post_id = $_POST['post_id'];
            $user_id = $_SESSION['user_id'];

            $comment = new Comment();
            $comment->create($contenido, $post_id, $user_id);

            header("Location: /posts/show?id=" . $post_id);
            exit;
        }
    }
}
