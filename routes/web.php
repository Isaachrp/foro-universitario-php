<?php

require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/PostController.php';
require_once __DIR__ . '/../app/controllers/CommentController.php';
require_once __DIR__ . '/../app/helpers/Auth.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base = '/foro-universitario-php/public';

if (strpos($uri, $base) === 0) {
    $uri = substr($uri, strlen($base));
}

if ($uri === '') {
    $uri = '/';
}

switch ($uri) {

    case '/':
        echo "Foro universitario";
        break;

    case '/register':
        // 🔒 Solo invitados
        Auth::requireGuest();
        (new AuthController())->register();
        break;

    case '/login':
        // 🔒 Solo invitados
        Auth::requireGuest();
        (new AuthController())->login();
        break;

    case '/logout':

        // 🔒 Solo usuarios autenticados
        Auth::requireAuth();

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();

        header("Location: /foro-universitario-php/public/login");
        exit;

    case '/dashboard':
        Auth::requireAuth();
        require_once __DIR__ . '/../app/views/dashboard.php';
        break;

    case '/posts':
        Auth::requireAuth(); // 🔥 decides si tu foro es privado
        (new PostController())->index();
        break;

    case '/posts/create':
        (new PostController())->create(); // ya protegido internamente
        break;

    case '/posts/show':
        (new PostController())->show($_GET['id'] ?? 0);
        break;

    case '/comments/create':
        (new CommentController())->create(); // ya protegido
        break;
    
    case '/posts/delete':
        (new PostController())->delete($_POST['id'] ?? 0);
        break;

    default:
        http_response_code(404);
        echo "Página no encontrada";
}