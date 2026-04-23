<?php

require_once __DIR__ . '/../models/User.php';

class UserController
{
    public function show($id)
    {
        $userModel = new User();

        $user = $userModel->getById((int)$id);

        if (!$user) {
            setFlash('error', 'Usuario no encontrado.');
            header("Location: /foro-universitario-php/public/posts");
            exit;
        }

        $posts = $userModel->getPosts((int)$id);
        $comments = $userModel->getComments((int)$id);

        require_once __DIR__ . '/../views/users/show.php';
    }
}