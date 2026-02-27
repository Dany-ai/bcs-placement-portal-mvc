<?php

require_once APP_ROOT . '/app/core/Model.php';

class Application extends Model
{
    /**
     * Student applies for a placement.
     * If they already applied, this does nothing (due to UNIQUE constraint).
     */
    public function apply($placementId, $studentId)
    {
        $stmt = $this->db->prepare("
            INSERT OR IGNORE INTO applications (placement_id, student_id, created_at)
            VALUES (:placement_id, :student_id, CURRENT_TIMESTAMP)
        ");

        $stmt->execute([
            ':placement_id' => $placementId,
            ':student_id'   => $studentId,
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * All applications for a given placement, joined to student + user info.
     */
    public function getApplicantsForPlacement($placementId)
    {
        $stmt = $this->db->prepare("
            SELECT
                a.*,
                s.name,
                s.skills,
                s.cv_filename,
                u.email
            FROM applications a
            JOIN students s ON s.id = a.student_id
            JOIN users u    ON u.id = s.user_id
            WHERE a.placement_id = :placement_id
            ORDER BY a.created_at ASC
        ");

        $stmt->execute([':placement_id' => $placementId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * All applications for one student, returned as an array
     * indexed by placement_id for easy lookup in views.
     */
    public function getApplicationsForStudentIndexed($studentId)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM applications
            WHERE student_id = :student_id
        ");

        $stmt->execute([':student_id' => $studentId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $byPlacement = [];
        foreach ($rows as $row) {
            $byPlacement[$row['placement_id']] = $row;
        }

        return $byPlacement;
    }
}
