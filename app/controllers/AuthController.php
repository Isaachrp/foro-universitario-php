<?php

require_once __DIR__ . '/../models/User.php';

class AuthController
{
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            csrf_verify();

            $nombre   = trim($_POST['nombre'] ?? '');
            $email    = strtolower(trim($_POST['email'] ?? ''));
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

            // 🔒 dominio institucional seguro
            $parts = explode('@', $email);
            $domain = $parts[1] ?? '';

            $allowedDomains = ['upatlacomulco.edu.mx'];

            if (!in_array($domain, $allowedDomains)) {
                setFlash('error', 'Debes usar un correo institucional.');
                header('Location: /foro-universitario-php/public/register');
                exit;
            }

            $user = new User();

            if ($user->existsByEmail($email)) {
                setFlash('warning', 'Ese correo ya está registrado.');
                header('Location: /foro-universitario-php/public/register');
                exit;
            }

            $token = bin2hex(random_bytes(32));

            // 🔒 validar creación
            $created = $user->createWithVerification($nombre, $email, $password, $token);

            if (!$created) {
                setFlash('error', 'Error al registrar usuario.');
                header('Location: /foro-universitario-php/public/register');
                exit;
            }

            // 📧 envío seguro
            try {
                if (!$this->sendVerificationEmail($email, $token)) {
                    throw new Exception('Error al enviar correo');
                }
            } catch (Exception $e) {
                setFlash('error', 'No se pudo enviar el correo.');
                header('Location: /foro-universitario-php/public/register');
                exit;
            }

            setFlash('success', 'Revisa tu correo para verificar tu cuenta.');
            require_once __DIR__ . '/../views/auth/verify_notice.php';
            exit;   
        }

        require_once __DIR__ . '/../views/auth/register.php';
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            csrf_verify();

            $email    = strtolower(trim($_POST['email'] ?? ''));
            $password = $_POST['password'] ?? '';

            $key = 'login_' . ($_SERVER['REMOTE_ADDR'] ?? 'guest');

            if (!rateLimitCheck($key)) {
                setFlash('error', 'Demasiados intentos. Intenta más tarde.');
                header('Location: /foro-universitario-php/public/login');
                exit;
            }

            if (!$email || !$password) {
                setFlash('error', 'Todos los campos son obligatorios.');
                header('Location: /foro-universitario-php/public/login');
                exit;
            }

            $user = (new User())->getByEmail($email);

            if ($user && password_verify($password, $user['password'])) {

                // 🔴 bloqueo si no verificado
                if (empty($user['email_verified_at'])) {
                    setFlash('warning', 'Debes verificar tu correo antes de iniciar sesión.');
                    header('Location: /foro-universitario-php/public/login');
                    exit;
                }

                rateLimitClear($key);

                session_regenerate_id(true);

                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_name'] = $user['nombre'];
                $_SESSION['user_role'] = $user['rol'];

                setFlash('success', 'Bienvenido de nuevo.');
                header('Location: /foro-universitario-php/public/dashboard');
                exit;
            }

            rateLimitHit($key);

            setFlash('error', 'Correo o contraseña incorrectos.');
            header('Location: /foro-universitario-php/public/login');
            exit;
        }

        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function verifyEmail()
    {
        $token = $_GET['token'] ?? '';

        if (!$token) {
            setFlash('error', 'Token inválido.');
            header('Location: /foro-universitario-php/public/login');
            exit;
        }

        $userModel = new User();
        $user = $userModel->getByVerificationToken($token);

        if (!$user) {
            require_once __DIR__ . '/../views/auth/verify_error.php';
            exit;
        }

        if (!empty($user['email_verified_at'])) {
            setFlash('warning', 'El correo ya fue verificado.');
            header('Location: /foro-universitario-php/public/login');
            exit;
        }

        $userModel->markEmailAsVerified($user['id']);

        setFlash('success', 'Correo verificado correctamente.');
        require_once __DIR__ . '/../views/auth/verify_success.php';
        exit;
    }

    private function sendVerificationEmail($email, $token)
    {
        $link = "http://localhost/foro-universitario-php/public/verify-email?token=$token";

        $subject = "Verifica tu cuenta";

        $body = "
            <h2>Verificación de cuenta</h2>
            <p>Haz clic en el siguiente enlace para activar tu cuenta:</p>
            <a href='$link'>$link</a>
            <br><br>
            <small>Si no solicitaste esto, ignora este mensaje.</small>
        ";

        return sendMail($email, $subject, $body);
    }
}