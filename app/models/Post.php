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

    public function getPaginated($limit, $offset)
    {
        $stmt = $this->db->prepare(
            "SELECT posts.*, users.nombre 
            FROM posts 
            JOIN users ON posts.user_id = users.id 
            ORDER BY created_at DESC 
            LIMIT ? OFFSET ?"
        );

        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countAll()
    {
        return $this->db->query("SELECT COUNT(*) FROM posts")->fetchColumn();
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

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM posts WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function update($id, $titulo, $contenido, $categoria, $archivo)
    {
        $stmt = $this->db->prepare(
            "UPDATE posts
            SET titulo = ?, contenido = ?, categoria = ?, archivo = ?
            WHERE id = ?"
        );

        return $stmt->execute([
            $titulo,
            $contenido,
            $categoria,
            $archivo,
            $id
        ]);
    }

    public function searchPaginated($search, $categoria, $limit, $offset)
    {
        $sql = "SELECT posts.*, users.nombre 
                FROM posts 
                JOIN users ON posts.user_id = users.id 
                WHERE 1=1";

        $params = [];

        // 🔍 Búsqueda
        if ($search) {
            $sql .= " AND (posts.titulo LIKE ? OR posts.contenido LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        // 🏷️ Filtro categoría
        if ($categoria) {
            $sql .= " AND posts.categoria = ?";
            $params[] = $categoria;
        }

        $sql .= " ORDER BY posts.created_at DESC LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($sql);

        // Bind dinámico
        $i = 1;
        foreach ($params as $param) {
            $stmt->bindValue($i++, $param, PDO::PARAM_STR);
        }

        $stmt->bindValue($i++, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue($i, (int)$offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countSearch($search, $categoria)
    {
        $sql = "SELECT COUNT(*) FROM posts WHERE 1=1";
        $params = [];

        if ($search) {
            $sql .= " AND (titulo LIKE ? OR contenido LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        if ($categoria) {
            $sql .= " AND categoria = ?";
            $params[] = $categoria;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchColumn();
    }
}