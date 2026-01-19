<?php
require_once __DIR__ . '/../config/database.php';

class Post
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->connect();
    }

    public function create($titulo, $contenido, $categoria, $archivo, $user_id)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO posts (titulo, contenido, categoria, archivo, user_id) VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([$titulo, $contenido, $categoria, $archivo, $user_id]);
    }

    public function getAll()
    {
        $stmt = $this->db->query(
            "SELECT posts.*, users.nombre FROM posts JOIN users ON posts.user_id = users.id ORDER BY created_at DESC"
        );
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM posts WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM posts WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
