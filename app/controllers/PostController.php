<?php

require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/Auth.php';

class PostController
{
    public function index()
    {
        $limit = 5;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $search = trim($_GET['search'] ?? '');
        $categoria = trim($_GET['categoria'] ?? '');

        $post = new Post();

        $posts = $post->searchPaginated($search, $categoria, $limit, $offset);
        $total = $post->countSearch($search, $categoria);

        $totalPages = ceil($total / $limit);

        require_once __DIR__ . '/../views/posts/index.php';
    }

    // 🔒 Verificar si el usuario está baneado
    private function checkBanned()
    {
        $user = (new User())->getById(Auth::id());

        if ($user && !empty($user['is_banned'])) {
            setFlash('error', 'Tu cuenta está bloqueada. No puedes realizar acciones.');
            header('Location: /foro-universitario-php/public/posts');
            exit;
        }
    }

    public function create()
    {
        Auth::requireAuth();
        $this->checkBanned();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $titulo    = trim($_POST['titulo'] ?? '');
            $contenido = trim($_POST['contenido'] ?? '');
            $categoria = trim($_POST['categoria'] ?? '');
            $archivo   = null;

            if ($titulo === '' || $contenido === '' || $categoria === '') {
                setFlash('error', 'Todos los campos obligatorios deben completarse.');
                header("Location: /foro-universitario-php/public/posts/create");
                exit;
            }

            // 📎 Subida de archivo
            if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === 0) {

                $fileTmp  = $_FILES['archivo']['tmp_name'];
                $fileSize = $_FILES['archivo']['size'];

                if ($fileSize > 5 * 1024 * 1024) {
                    setFlash('error', 'El archivo es demasiado grande.');
                    header("Location: /foro-universitario-php/public/posts/create");
                    exit;
                }

                $extension = strtolower(pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'pdf'];

                if (!in_array($extension, $allowed)) {
                    setFlash('error', 'Tipo de archivo no permitido.');
                    header("Location: /foro-universitario-php/public/posts/create");
                    exit;
                }

                $extension = $extension === 'jpeg' ? 'jpg' : $extension;
                $nombreSeguro = bin2hex(random_bytes(16)) . '.' . $extension;

                $uploadDir = __DIR__ . '/../../public/uploads/';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $destino = $uploadDir . $nombreSeguro;

                if (!move_uploaded_file($fileTmp, $destino)) {
                    setFlash('error', 'Error al subir el archivo.');
                    exit;
                }

                $archivo = $nombreSeguro;
            }

            (new Post())->create(
                $titulo,
                $contenido,
                $categoria,
                $archivo,
                Auth::id()
            );

            setFlash('success', 'Publicación creada correctamente.');
            header("Location: /foro-universitario-php/public/posts");
            exit;
        }

        require_once __DIR__ . '/../views/posts/create.php';
    }

    public function delete($id)
    {
        Auth::requireAuth();
        $this->checkBanned();
        csrf_verify();

        $post = new Post();
        $postData = $post->getById((int)$id);

        if (!$postData) {
            setFlash('error', 'Post no encontrado.');
            header("Location: /foro-universitario-php/public/posts");
            exit;
        }

        Auth::requireOwnerOrAdmin($postData['user_id']);

        $post->delete((int)$id);

        setFlash('success', 'Post eliminado.');
        header("Location: /foro-universitario-php/public/posts");
        exit;
    }

    public function update()
    {
        Auth::requireAuth();
        $this->checkBanned();
        csrf_verify();

        $id        = (int) ($_POST['id'] ?? 0);
        $titulo    = trim($_POST['titulo'] ?? '');
        $contenido = trim($_POST['contenido'] ?? '');
        $categoria = trim($_POST['categoria'] ?? '');

        if ($id <= 0 || !$titulo || !$contenido || !$categoria) {
            setFlash('error', 'Datos inválidos.');
            header("Location: /foro-universitario-php/public/posts");
            exit;
        }

        $postModel = new Post();
        $post = $postModel->getById($id);

        if (!$post) {
            setFlash('error', 'Post no existe.');
            exit;
        }

        Auth::requireOwnerOrAdmin($post['user_id']);

        $postModel->update($id, $titulo, $contenido, $categoria, $post['archivo']);

        setFlash('success', 'Publicación actualizada.');
        header("Location: /foro-universitario-php/public/posts/show?id=" . $id);
        exit;
    }

    public function show($id)
    {
        $postModel = new Post();
        $postData = $postModel->getById((int)$id);

        if (!$postData) {
            setFlash('error', 'La publicación no existe.');
            header("Location: /foro-universitario-php/public/posts");
            exit;
        }

        $commentModel = new Comment();
        $comments = $commentModel->getByPostId((int)$id);

        require_once __DIR__ . '/../views/posts/show.php';
    }

    public function edit($id)
    {
        Auth::requireAuth();
        $this->checkBanned();

        $postModel = new Post();
        $post = $postModel->getById((int)$id);

        if (!$post) {
            setFlash('error', 'Publicación no encontrada.');
            header("Location: /foro-universitario-php/public/posts");
            exit;
        }

        // 🔒 Permiso: solo dueño o admin
        Auth::requireOwnerOrAdmin($post['user_id']);

        require_once __DIR__ . '/../views/posts/edit.php';
    }
}