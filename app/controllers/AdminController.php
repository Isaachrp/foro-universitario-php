<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/Auth.php';

class AdminController
{
    public function dashboard()
    {
        Auth::requireAuth();

        // 🔒 solo admin
        if (!Auth::isAdmin()) {
            setFlash('error', 'No tienes permisos.');
            header("Location: /foro-universitario-php/public/posts");
            exit;
        }

        $users = (new User())->getAll(); // 🔥 aquí se genera $users

        require_once __DIR__ . '/../views/dashboard.php';
    }

    public function ban()
    {
        Auth::requireAuth();

        if (!Auth::isAdmin()) {
            exit('Acceso denegado');
        }

        $id = (int) ($_POST['id'] ?? 0);

        (new User())->ban($id);

        header("Location: /foro-universitario-php/public/dashboard");
        exit;
    }

    public function unban()
    {
        Auth::requireAuth();

        if (!Auth::isAdmin()) {
            exit('Acceso denegado');
        }

        $id = (int) ($_POST['id'] ?? 0);

        (new User())->unban($id);

        header("Location: /foro-universitario-php/public/dashboard");
        exit;
    }
}