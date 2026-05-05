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

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function existsByEmail(string $email): bool
    {
        return $this->getByEmail($email) !== false;
    }

    public function getById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM users WHERE id = ? LIMIT 1"
        );

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🔥 NECESARIO PARA ADMIN PANEL
    public function getAll(): array
    {
        $stmt = $this->db->query(
            "SELECT id, nombre, email, rol, is_banned, banned_reason, created_at 
             FROM users 
             ORDER BY created_at DESC"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPosts(int $user_id): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM posts 
             WHERE user_id = ? 
             ORDER BY created_at DESC"
        );

        $stmt->execute([$user_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =============================
    // 🔐 EMAIL VERIFICATION
    // =============================

    public function createWithVerification(
        string $nombre,
        string $email,
        string $password,
        string $token
    ): bool {
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

    public function getByVerificationToken(string $token): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM users WHERE verification_token = ? LIMIT 1"
        );

        $stmt->execute([$token]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function markEmailAsVerified(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE users 
             SET email_verified_at = NOW(), verification_token = NULL 
             WHERE id = ?"
        );

        return $stmt->execute([$id]);
    }

    // =============================
    // 🚫 BAN SYSTEM
    // =============================

    public function ban(int $id, string $reason = null): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE users 
             SET is_banned = 1, banned_reason = ?, banned_at = NOW() 
             WHERE id = ?"
        );

        return $stmt->execute([$reason, $id]);
    }

    public function unban(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE users 
             SET is_banned = 0, banned_reason = NULL, banned_at = NULL 
             WHERE id = ?"
        );

        return $stmt->execute([$id]);
    }

    // 🔥 UTILIDAD EXTRA (MUY ÚTIL)
    public function isBanned(int $id): bool
    {
        $stmt = $this->db->prepare(
            "SELECT is_banned FROM users WHERE id = ? LIMIT 1"
        );

        $stmt->execute([$id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return !empty($result['is_banned']);
    }

    public function update(int $id, string $nombre, string $email, string $rol): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE users 
            SET nombre = ?, email = ?, rol = ? 
            WHERE id = ?"
        );

        return $stmt->execute([
            trim($nombre),
            strtolower(trim($email)),
            $rol,
            $id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM users WHERE id = ?"
        );

        return $stmt->execute([$id]);
    }
}