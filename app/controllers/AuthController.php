<?php
require_once __DIR__ . '/../models/User.php';

class AuthController
{

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            if (empty($nombre) || empty($email) || empty($password)) {
                die("Todos los campos son obligatorios");
            }

            $user = new User();
            $user->create($nombre, $email, $password);

            echo "Usuario registrado correctamente";
            return;
        }

        require_once __DIR__ . '/../views/auth/register.php';
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            if (empty($email) || empty($password)) {
                die("Todos los campos son obligatorios");
            }

            $user = (new User())->getByEmail($email);

            if ($user && password_verify($password, $user['password'])) {

                session_regenerate_id(true); //  evita session fixation
                
                // Credenciales correctas
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nombre'];
                $_SESSION['user_role'] = $user['rol'];

                header("Location: /dashboard");
                exit;
            } else {
                echo "Correo o contraseña incorrectos";
            }
        }

        require_once __DIR__ . '/../views/auth/login.php';
    }
}
