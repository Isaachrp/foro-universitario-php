<?php

require_once __DIR__ . '/../config/database.php';

class Post
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function create(
        string $titulo,
        string $contenido,
        string $categoria,
        ?string $archivo,
        int $user_id
    ): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO posts (titulo, contenido, categoria, archivo, user_id)
             VALUES (?, ?, ?, ?, ?)"
        );

        return $stmt->execute([
            trim($titulo),
            trim($contenido),
            trim($categoria),
            $archivo,
            $user_id
        ]);
    }

    public function getAll(): array
    {
        $stmt = $this->db->query(
            "SELECT posts.*, users.nombre
             FROM posts
             JOIN users ON posts.user_id = users.id
             ORDER BY posts.created_at DESC"
        );

        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT posts.*, users.nombre
             FROM posts
             JOIN users ON posts.user_id = users.id
             WHERE posts.id = ?
             LIMIT 1"
        );

        $stmt->execute([$id]);

        return $stmt->fetch();
    }
}