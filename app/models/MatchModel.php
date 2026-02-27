<?php

require_once APP_ROOT . '/app/core/Model.php';

class MatchModel extends Model
{
    public function saveMatch($studentId, $placementId, $score)
    {
        $stmt = $this->db->prepare("
            INSERT INTO matches (student_id, placement_id, score, created_at)
            VALUES (:student_id, :placement_id, :score, CURRENT_TIMESTAMP)
        ");

        return $stmt->execute([
            ':student_id'   => $studentId,
            ':placement_id' => $placementId,
            ':score'        => $score
        ]);
    }

    public function getMatchesForStudent($studentId)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, e.company_name, m.score
            FROM matches m
            JOIN placements p ON p.id = m.placement_id
            JOIN employers  e ON e.id = p.employer_id
            WHERE m.student_id = :student_id
            ORDER BY m.score DESC, m.created_at DESC
        ");

        $stmt->execute([':student_id' => $studentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMatchesForPlacement($placementId)
    {
        $stmt = $this->db->prepare("
            SELECT s.*, m.score
            FROM matches m
            JOIN students s ON s.id = m.student_id
            WHERE m.placement_id = :placement_id
            ORDER BY m.score DESC, m.created_at DESC
        ");

        $stmt->execute([':placement_id' => $placementId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteMatchesForStudent($studentId)
    {
        $stmt = $this->db->prepare("DELETE FROM matches WHERE student_id = :student_id");
        return $stmt->execute([':student_id' => $studentId]);
    }

    public function deleteMatchesForPlacement($placementId)
    {
        $stmt = $this->db->prepare("DELETE FROM matches WHERE placement_id = :placement_id");
        return $stmt->execute([':placement_id' => $placementId]);
    }
}
