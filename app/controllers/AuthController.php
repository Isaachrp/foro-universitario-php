<?php

require_once __DIR__ . '/../models/User.php';

class AuthController
{
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            csrf_verify();
            $nombre   = trim($_POST['nombre'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (!$nombre || !$email || !$password) {
                setFlash('error', 'Todos los campos son obligatorios.');
                header('Location: /foro-universitario-php/public/register');
                exit;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                setFlash('error', 'Correo inválido.');
                header('Location: /foro-universitario-php/public/register');
                exit;
            }

            $user = new User();

            if ($user->existsByEmail($email)) {
                setFlash('warning', 'Ese correo ya está registrado.');
                header('Location: /foro-universitario-php/public/register');
                exit;
            }

            $user->create($nombre, $email, $password);

            setFlash('success', 'Usuario registrado correctamente.');
            header('Location: /foro-universitario-php/public/login');
            exit;
        }

        require_once __DIR__ . '/../views/auth/register.php';
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            csrf_verify();
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (!$email || !$password) {
                setFlash('error', 'Todos los campos son obligatorios.');
                header('Location: /foro-universitario-php/public/login');
                exit;
            }

            $user = (new User())->getByEmail($email);

            if ($user && password_verify($password, $user['password'])) {

                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_name'] = $user['nombre'];
                $_SESSION['user_role'] = $user['rol'];

                setFlash('success', 'Bienvenido de nuevo.');
                header('Location: /foro-universitario-php/public/dashboard');
                exit;
            }

            setFlash('error', 'Correo o contraseña incorrectos.');
            header('Location: /foro-universitario-php/public/login');
            exit;
        }

        require_once __DIR__ . '/../views/auth/login.php';
    }
}