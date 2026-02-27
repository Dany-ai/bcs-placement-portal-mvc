<?php

require_once APP_ROOT . '/app/core/Model.php';

class User extends Model
{
    public function findByEmail($email)
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create($email, $passwordHash, $role, $name = null)
    {
        $stmt = $this->db->prepare("
            INSERT INTO users (email, password, role, name, created_at)
            VALUES (:email, :password, :role, :name, CURRENT_TIMESTAMP)
        ");

        $stmt->execute([
            ':email'    => $email,
            ':password' => $passwordHash,
            ':role'     => $role,
            ':name'     => $name
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function getUserById($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => (int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getFirstByRole($role)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE role = :role
            ORDER BY id DESC
            LIMIT 1
        ");

        $stmt->execute([':role' => $role]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function updatePasswordHash(int $userId, string $hash): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET password = :password
            WHERE id = :id
        ");

        return $stmt->execute([
            ':password' => $hash,
            ':id'       => $userId
        ]);
    }
}