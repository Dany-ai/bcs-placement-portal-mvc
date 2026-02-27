<?php

require_once APP_ROOT . '/app/core/Model.php';

class Student extends Model
{
    /**
     * Create a student profile for a user.
     */
    public function createProfile($userId, array $data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO students (user_id, name, phone, address, skills, cv_filename)
            VALUES (:user_id, :name, :phone, :address, :skills, :cv_filename)
        ");

        $stmt->execute([
            ':user_id'     => $userId,
            ':name'        => $data['name'] ?? '',
            ':phone'       => $data['phone'] ?? '',
            ':address'     => $data['address'] ?? '',
            ':skills'      => $data['skills'] ?? '',
            ':cv_filename' => $data['cv_filename'] ?? null,
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Find a student profile by the linked user_id.
     */
    public function findByUserId($userId)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM students
            WHERE user_id = :user_id
            LIMIT 1
        ");

        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update a student profile (by user_id).
     */
    public function updateProfile($userId, array $data)
    {
        $stmt = $this->db->prepare("
            UPDATE students
            SET name = :name,
                phone = :phone,
                address = :address,
                skills = :skills
            WHERE user_id = :user_id
        ");

        return $stmt->execute([
            ':name'    => $data['name'] ?? '',
            ':phone'   => $data['phone'] ?? '',
            ':address' => $data['address'] ?? '',
            ':skills'  => $data['skills'] ?? '',
            ':user_id' => $userId,
        ]);
    }

    /**
     * Update only the CV filename for this user.
     */
    public function updateCv($userId, $filename)
    {
        $stmt = $this->db->prepare("
            UPDATE students
            SET cv_filename = :cv_filename
            WHERE user_id = :user_id
        ");

        return $stmt->execute([
            ':cv_filename' => $filename,
            ':user_id'     => $userId,
        ]);
    }

    /**
     * List all students with their linked user account (for admin dashboard).
     * Returns: name, email, user_id, etc.
     */
    public function allWithUser()
    {
        $stmt = $this->db->prepare("
            SELECT
                s.*,
                u.id    AS user_id,
                u.email AS email
            FROM students s
            JOIN users u ON u.id = s.user_id
            ORDER BY s.name ASC
        ");

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Search students by name or email (for admin dashboard search box).
     */
    public function searchWithUser($term)
    {
        $like = '%' . $term . '%';

        $stmt = $this->db->prepare("
            SELECT
                s.*,
                u.id    AS user_id,
                u.email AS email
            FROM students s
            JOIN users u ON u.id = s.user_id
            WHERE s.name  LIKE :q
               OR u.email LIKE :q
            ORDER BY s.name ASC
        ");

        $stmt->execute([':q' => $like]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
