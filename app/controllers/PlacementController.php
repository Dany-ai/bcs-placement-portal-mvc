<?php

require_once APP_ROOT . '/app/core/Controller.php';
require_once APP_ROOT . '/app/core/Session.php';
require_once APP_ROOT . '/app/core/Auth.php';

class PlacementController extends Controller
{
    /**
     * Public placement listings with filters.
     */
    public function index()
    {
        Session::init();

        $placementModel = $this->model('Placement');

        // Collect filters from query string (?q=...&location=... etc.)
        $filters = [
            'q'        => trim($_GET['q'] ?? ''),
            'location' => trim($_GET['location'] ?? ''),
            'company'  => trim($_GET['company'] ?? ''),
            'skills'   => trim($_GET['skills'] ?? ''),
        ];

        $placements = $placementModel->search($filters);

        // 🔹 If a student is logged in, load their applications so we can
        //     show "Apply" / "Applied" buttons in the placements view.
        $applicationsByPlacement = [];
        $currentUser             = Auth::user();

        if ($currentUser && ($currentUser['role'] === 'student')) {
            $studentModel     = $this->model('Student');
            $applicationModel = $this->model('Application');

            $studentProfile = $studentModel->findByUserId($currentUser['id']);
            if ($studentProfile) {
                // Returns [placement_id => application_row, ...]
                $applicationsByPlacement = $applicationModel
                    ->getApplicationsForStudentIndexed($studentProfile['id']);
            }
        }

        $this->view('placement/index', [
            'placements'              => $placements,
            'filters'                 => $filters,
            'applicationsByPlacement' => $applicationsByPlacement,
        ]);
    }

    /**
     * Optional: view a single placement.
     */
    public function show($id)
    {
        Session::init();

        $placementModel = $this->model('Placement');
        $placement      = $placementModel->find($id);

        if (!$placement) {
            header('Location: ' . URL_ROOT . '/placement/index');
            exit;
        }

        // (Optional) also show Apply/Applied on the detail page:
        $applied = false;
        $currentUser = Auth::user();

        if ($currentUser && $currentUser['role'] === 'student') {
            $studentModel     = $this->model('Student');
            $applicationModel = $this->model('Application');

            $studentProfile = $studentModel->findByUserId($currentUser['id']);
            if ($studentProfile) {
                $appsByPlacement = $applicationModel
                    ->getApplicationsForStudentIndexed($studentProfile['id']);
                $applied = !empty($appsByPlacement[(int)$placement['id']] ?? null);
            }
        }

        $this->view('placement/show', [
            'placement' => $placement,
            'applied'   => $applied,
        ]);
    }
}
