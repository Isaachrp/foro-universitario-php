<?php

require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/PostController.php';
require_once __DIR__ . '/../app/controllers/CommentController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($uri) {
    case '/':
        echo "Foro universitario";
        break;

    case '/register':
        $controller = new AuthController();
        $controller->register();
        break;

    case '/login':
        $controller = new AuthController();
        $controller->login();
        break;

    case '/logout':
        session_start();
        session_unset();
        session_destroy();
        header("Location: /login");
        exit;

    case '/dashboard':
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }
        require_once __DIR__ . '/../app/views/dashboard.php';
        break;


    case '/posts':
        $controller = new PostController();
        $controller->index();
        break;

    case '/posts/create':
        $controller = new PostController();
        $controller->create();
        break;

    case '/posts/show':
        $controller = new PostController();
        $controller->show($_GET['id'] ?? 0);
        break;

    case '/comments/create':
        $controller = new CommentController();
        $controller->create();
        break;



    default:
        http_response_code(404);
        echo "Página no encontrada";
}
