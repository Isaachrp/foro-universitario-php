<?php

require_once __DIR__ . '/../config/database.php';

class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function create(string $nombre, string $email, string $password): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO users (nombre, email, password) VALUES (?, ?, ?)"
        );

        return $stmt->execute([
            trim($nombre),
            strtolower(trim($email)),
            password_hash($password, PASSWORD_DEFAULT)
        ]);
    }

    public function getByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM users WHERE email = ? LIMIT 1"
        );

        $stmt->execute([
            strtolower(trim($email))
        ]);

        return $stmt->fetch();
    }

    public function existsByEmail(string $email): bool
    {
        return $this->getByEmail($email) !== false;
    }

    public function getById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT id, nombre, email, created_at FROM users WHERE id = ? LIMIT 1"
        );

        $stmt->execute([$id]);

        return $stmt->fetch();
    }

    public function getPosts(int $user_id): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM posts 
            WHERE user_id = ? 
            ORDER BY created_at DESC"
        );

        $stmt->execute([$user_id]);

        return $stmt->fetchAll();
    }

    public function getComments(int $user_id): array
    {
        $stmt = $this->db->prepare(
            "SELECT comments.*, posts.titulo 
            FROM comments
            JOIN posts ON comments.post_id = posts.id
            WHERE comments.user_id = ?
            ORDER BY comments.created_at DESC"
        );

        $stmt->execute([$user_id]);

        return $stmt->fetchAll();
    }

    public function createWithVerification(string $nombre, string $email, string $password, string $token): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO users (nombre, email, password, verification_token) 
            VALUES (?, ?, ?, ?)"
        );

        return $stmt->execute([
            trim($nombre),
            strtolower(trim($email)),
            password_hash($password, PASSWORD_DEFAULT),
            $token
        ]);
    }

    public function getByVerificationToken(string $token)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM users WHERE verification_token = ? LIMIT 1"
        );

        $stmt->execute([$token]);

        return $stmt->fetch();
    }

    public function markEmailAsVerified(int $id)
    {
        $stmt = $this->db->prepare(
            "UPDATE users 
            SET email_verified_at = NOW(), verification_token = NULL 
            WHERE id = ?"
        );

        return $stmt->execute([$id]);
    }
}