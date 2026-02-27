<?php

require_once APP_ROOT . '/app/core/Model.php';

class User extends Model
{
    /**
     * Find a user by email.
     */
    public function findByEmail($email)
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new user account, including optional name.
     */
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

        return $this->db->lastInsertId();
    }

    /**
     * Get a user by ID (useful for chat & dashboards).
     */
    public function getUserById($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get the "primary" user with a specific role.
     * We use the *newest* admin as the career support account.
     */
    public function getFirstByRole($role)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE role = :role
            ORDER BY id DESC   -- newest first, not oldest
            LIMIT 1
        ");

        $stmt->execute([':role' => $role]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
