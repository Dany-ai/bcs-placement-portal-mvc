<?php

require_once APP_ROOT . '/app/core/Model.php';

class Placement extends Model
{
    /**
     * Create a new placement for an employer.
     */
    public function create($employerId, $data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO placements 
                (employer_id, title, description, skills_required, salary, location, start_date, end_date, status, created_at)
             VALUES
                (:employer_id, :title, :description, :skills_required, :salary, :location, :start_date, :end_date, 'pending', CURRENT_TIMESTAMP)"
        );

        $stmt->execute([
            ':employer_id'     => $employerId,
            ':title'           => $data['title'],
            ':description'     => $data['description'],
            ':skills_required' => $data['skills_required'] ?? null,
            ':salary'          => $data['salary'] ?? null,
            ':location'        => $data['location'] ?? null,
            ':start_date'      => !empty($data['start_date']) ? $data['start_date'] : null,
            ':end_date'        => !empty($data['end_date']) ? $data['end_date'] : null,
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Update an existing placement, only if it belongs to the employer.
     */
    public function update($placementId, $employerId, $data)
    {
        $stmt = $this->db->prepare(
            "UPDATE placements
             SET title           = :title,
                 description     = :description,
                 skills_required = :skills_required,
                 salary          = :salary,
                 location        = :location,
                 start_date      = :start_date,
                 end_date        = :end_date
             WHERE id           = :id
               AND employer_id  = :employer_id"
        );

        return $stmt->execute([
            ':id'              => $placementId,
            ':employer_id'     => $employerId,
            ':title'           => $data['title'],
            ':description'     => $data['description'],
            ':skills_required' => $data['skills_required'] ?? null,
            ':salary'          => $data['salary'] ?? null,
            ':location'        => $data['location'] ?? null,
            ':start_date'      => !empty($data['start_date']) ? $data['start_date'] : null,
            ':end_date'        => !empty($data['end_date']) ? $data['end_date'] : null,
        ]);
    }

    /**
     * Delete a placement that belongs to the employer.
     */
    public function delete($placementId, $employerId)
    {
        $stmt = $this->db->prepare(
            "DELETE FROM placements WHERE id = :id AND employer_id = :employer_id"
        );

        return $stmt->execute([
            ':id'          => $placementId,
            ':employer_id' => $employerId,
        ]);
    }

    /**
     * All placements for one employer (any status).
     */
    public function findByEmployer($employerId)
    {
        $stmt = $this->db->prepare(
            "SELECT *
             FROM placements
             WHERE employer_id = :employer_id
             ORDER BY created_at DESC"
        );

        $stmt->execute([':employer_id' => $employerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Public placements list (typically only approved).
     * Adjust WHERE clause if you want to show only approved placements.
     */
    public function all()
    {
        $stmt = $this->db->query(
            "SELECT p.*, e.company_name
             FROM placements p
             JOIN employers e ON e.id = p.employer_id
             WHERE p.status = 'approved'
             ORDER BY p.created_at DESC"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Filtered search for placements for the public placements page.
     */
    public function search(array $filters = [])
    {
        $sql = "SELECT p.*, e.company_name
                FROM placements p
                JOIN employers e ON e.id = p.employer_id
                WHERE p.status = 'approved'";
        $params = [];

        if (!empty($filters['q'])) {
            $sql .= " AND (p.title LIKE :q OR p.description LIKE :q)";
            $params[':q'] = '%' . $filters['q'] . '%';
        }

        if (!empty($filters['location'])) {
            $sql .= " AND p.location LIKE :location";
            $params[':location'] = '%' . $filters['location'] . '%';
        }

        if (!empty($filters['company'])) {
            $sql .= " AND e.company_name LIKE :company";
            $params[':company'] = '%' . $filters['company'] . '%';
        }

        if (!empty($filters['skills'])) {
            $sql .= " AND p.skills_required LIKE :skills";
            $params[':skills'] = '%' . $filters['skills'] . '%';
        }

        $sql .= " ORDER BY p.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 🔹 NEW: Pending placements for the admin dashboard.
     * Treat NULL status as pending just in case.
     */
    public function findPending()
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, e.company_name
             FROM placements p
             JOIN employers e ON e.id = p.employer_id
             WHERE p.status = 'pending' OR p.status IS NULL
             ORDER BY p.created_at DESC"
        );

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Set placement status (approved / rejected) by admin.
     * Expects columns: status, approved_at, rejected_at, reviewed_by, reviewed_at.
     */
    public function setStatus($placementId, $status, $adminUserId)
    {
        $now = date('Y-m-d H:i:s');

        // Base fields
        $sql = "UPDATE placements
                SET status = :status,
                    reviewed_by = :reviewed_by,
                    reviewed_at = :reviewed_at";

        // Extra timestamp depending on status
        if ($status === 'approved') {
            $sql .= ", approved_at = :ts";
        } elseif ($status === 'rejected') {
            $sql .= ", rejected_at = :ts";
        }

        $sql .= " WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        $params = [
            ':status'      => $status,
            ':reviewed_by' => $adminUserId,
            ':reviewed_at' => $now,
            ':id'          => $placementId,
        ];

        if ($status === 'approved' || $status === 'rejected') {
            $params[':ts'] = $now;
        }

        return $stmt->execute($params);
    }

    /**
     * Find a single placement with employer info.
     */
    public function find($id)
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, e.company_name
             FROM placements p
             JOIN employers e ON e.id = p.employer_id
             WHERE p.id = :id"
        );

        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
