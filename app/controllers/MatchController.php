<?php

require_once APP_ROOT . '/app/core/Controller.php';
require_once APP_ROOT . '/app/core/Session.php';
require_once APP_ROOT . '/app/core/Auth.php';
require_once APP_ROOT . '/app/core/Database.php';

class MatchController extends Controller
{
    /**
     * Generate matches for ALL students & placements
     * based on overlapping comma-separated skills.
     *
     * - Normalises skills (case-insensitive, trims spaces)
     * - Skips students with no skills
     * - Skips placements with no skills
     * - Only saves matches with score > 0
     */
    public function generate()
    {
        Session::init();

        $studentModel   = $this->model('Student');
        $placementModel = $this->model('Placement');
        $matchModel     = $this->model('MatchModel');

        $students   = $studentModel->all();
        $placements = $placementModel->all();

        // Clear ALL existing matches before recalculating everything
        $db = Database::getInstance()->getConnection();
        $db->exec("DELETE FROM matches");

        foreach ($students as $student) {
            $this->runMatchingForStudent($student, $placements, $matchModel);
        }

        $this->view('match/overview', [
            'message' => 'Matching completed. Students and employers can now view their matches.'
        ]);
    }

    /**
     * Regenerate matches ONLY for one student.
     * Call this from the student profile update action so
     * matches change automatically when they edit/remove skills.
     */
    public function regenerateForStudent($studentId)
    {
        Session::init();

        $placementModel = $this->model('Placement');
        $matchModel     = $this->model('MatchModel');

        // Load this student directly from the database (no Student::find())
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM students WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $studentId]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$student) {
            // No such student – nothing to do
            return;
        }

        $placements = $placementModel->all();

        // Delete ONLY this student's existing matches
        $matchModel->deleteMatchesForStudent($studentId);

        // Recreate matches just for this student
        $this->runMatchingForStudent($student, $placements, $matchModel);
    }

    /**
     * Core matching logic for a single student against a list of placements.
     */
    private function runMatchingForStudent(array $student, array $placements, $matchModel)
    {
        // Normalise student skills
        $studentSkills = $this->normaliseSkills($student['skills'] ?? '');
        if (empty($studentSkills)) {
            // Student has no skills -> no matches at all
            return;
        }

        foreach ($placements as $placement) {
            // Normalise placement required skills
            $requiredSkills = $this->normaliseSkills($placement['skills_required'] ?? '');
            if (empty($requiredSkills)) {
                // Placement has no required skills -> skip
                continue;
            }

            // Find overlap between student skills and required skills
            $overlap = array_intersect($studentSkills, $requiredSkills);
            if (empty($overlap)) {
                continue;
            }

            // Simple score: what % of required skills does the student have?
            $score = (int) round(count($overlap) / max(count($requiredSkills), 1) * 100);

            if ($score > 0) {
                $matchModel->saveMatch($student['id'], $placement['id'], $score);
            }
        }
    }

    /**
     * Turn a raw skills string into a normalised list of tokens.
     *
     * Example:
     *   "Programming/software development, Testing, Systems design"
     *   -> ["programming/software development", "testing", "systems design"]
     */
    private function normaliseSkills($raw)
    {
        if (!$raw) {
            return [];
        }

        // Split on commas or semicolons
        $tokens = preg_split('/[,;]+/', strtolower($raw));

        $skills = [];
        foreach ($tokens as $token) {
            $token = trim($token);
            if ($token === '') {
                continue;
            }

            // Optional: strip any SFIA codes in brackets, e.g. "(PROG:3)"
            $token = preg_replace('/\([^)]*\)/', '', $token);
            $token = trim($token);

            if ($token !== '') {
                $skills[] = $token;
            }
        }

        // Remove duplicates
        return array_values(array_unique($skills));
    }
}
