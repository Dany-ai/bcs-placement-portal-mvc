<?php

require_once APP_ROOT . '/app/core/Model.php';

class Employer extends Model
{
    /**
     * Create an employer profile for a given user.
     */
    public function createProfile($userId, $data)
    {
        $stmt = $this->db->prepare(
            'INSERT INTO employers (user_id, company_name, contact_name, phone, address) 
             VALUES (:user_id, :company_name, :contact_name, :phone, :address)'
        );

        $stmt->execute([
            'user_id'      => $userId,
            'company_name' => $data['company_name'],
            'contact_name' => $data['contact_name'],
            'phone'        => $data['phone'],
            'address'      => $data['address'],
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Update the employer profile belonging to a user.
     */
    public function updateProfile($userId, $data)
    {
        $stmt = $this->db->prepare(
            'UPDATE employers 
             SET company_name = :company_name,
                 contact_name = :contact_name,
                 phone        = :phone,
                 address      = :address
             WHERE user_id   = :user_id'
        );

        return $stmt->execute([
            'user_id'      => $userId,
            'company_name' => $data['company_name'],
            'contact_name' => $data['contact_name'],
            'phone'        => $data['phone'],
            'address'      => $data['address'],
        ]);
    }

    /**
     * Find employer row by the owning user id.
     */
    public function findByUserId($userId)
    {
        $stmt = $this->db->prepare('SELECT * FROM employers WHERE user_id = :user_id LIMIT 1');
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Find employer by its primary key.
     */
    public function find($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM employers WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
