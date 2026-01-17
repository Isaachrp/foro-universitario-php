<?php
require_once __DIR__ . '/../config/database.php';

class User
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->connect();
    }

    public function create($nombre, $email, $password)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO users (nombre, email, password) VALUES (?, ?, ?)"
        );

        return $stmt->execute([
            $nombre,
            $email,
            password_hash($password, PASSWORD_DEFAULT)
        ]);
    }

    public function getByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(); // retorna el usuario o false
    }
}
