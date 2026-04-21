<?php

require_once __DIR__ . '/../models/User.php';

class AuthController
{
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre   = trim($_POST['nombre'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (!$nombre || !$email || !$password) {
                exit('Todos los campos son obligatorios.');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                exit('Correo inválido.');
            }

            $user = new User();

            if ($user->existsByEmail($email)) {
                exit('Ese correo ya está registrado.');
            }

            $user->create($nombre, $email, $password);

            header('Location: /foro-universitario-php/public/login');
            exit;
        }

        require_once __DIR__ . '/../views/auth/register.php';
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (!$email || !$password) {
                exit('Todos los campos son obligatorios.');
            }

            $user = (new User())->getByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_name'] = $user['nombre'];
                $_SESSION['user_role'] = $user['rol'];

                header('Location: /foro-universitario-php/public/dashboard');
                exit;
            }

            exit('Correo o contraseña incorrectos.');
        }

        require_once __DIR__ . '/../views/auth/login.php';
    }
}