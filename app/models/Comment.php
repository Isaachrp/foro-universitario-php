<?php

require_once __DIR__ . '/../config/database.php';

class Comment
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function create(string $contenido, int $post_id, int $user_id): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO comments (contenido, post_id, user_id)
             VALUES (?, ?, ?)"
        );

        return $stmt->execute([
            trim($contenido),
            $post_id,
            $user_id
        ]);
    }

    public function getByPostId(int $post_id): array
    {
        $stmt = $this->db->prepare(
            "SELECT comments.*, users.nombre
             FROM comments
             JOIN users ON comments.user_id = users.id
             WHERE comments.post_id = ?
             ORDER BY comments.created_at ASC"
        );

        $stmt->execute([$post_id]);

        return $stmt->fetchAll();
    }

    // 🔒 Obtener un comentario por ID (para validar permisos)
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM comments WHERE id = ?"
        );

        $stmt->execute([$id]);

        $result = $stmt->fetch();

        return $result ?: null;
    }

    // 🔒 Eliminar comentario
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM comments WHERE id = ?"
        );

        return $stmt->execute([$id]);
    }

    public function update(int $id, string $contenido): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE comments SET contenido = ? WHERE id = ?"
        );

        return $stmt->execute([
            trim($contenido),
            $id
        ]);
    }
}