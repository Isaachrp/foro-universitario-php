<?php
require_once __DIR__ . '/../config/database.php';

class Comment {
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    public function create($contenido, $post_id, $user_id) {
        $stmt = $this->db->prepare(
            "INSERT INTO comments (contenido, post_id, user_id) VALUES (?, ?, ?)"
        );
        return $stmt->execute([$contenido, $post_id, $user_id]);
    }

    public function getByPostId($post_id) {
        $stmt = $this->db->prepare(
            "SELECT comments.*, users.nombre FROM comments 
             JOIN users ON comments.user_id = users.id 
             WHERE post_id = ? ORDER BY created_at ASC"
        );
        $stmt->execute([$post_id]);
        return $stmt->fetchAll();
    }
}
