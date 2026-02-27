<?php

require_once APP_ROOT . '/app/core/Controller.php';
require_once APP_ROOT . '/app/core/Session.php';
require_once APP_ROOT . '/app/core/Auth.php';
require_once APP_ROOT . '/app/core/Database.php';

class EmployerController extends Controller
{
    public function index()
    {
        // /employer behaves the same as /employer/dashboard
        $this->dashboard();
    }

    /**
     * Main employer dashboard.
     * Shows organisation info, placements, and inbox messages.
     */
    public function dashboard()
    {
        Session::init();
        Auth::requireRole('employer');

        $employerModel  = $this->model('Employer');
        $placementModel = $this->model('Placement');
        $messageModel   = $this->model('Message');

        $user       = Auth::user();
        $employer   = $employerModel->findByUserId($user['id']);
        $placements = $placementModel->findByEmployer($employer['id']);

        // Messages for this employer user
        $messages    = $messageModel->getForUser($user['id']);
        $unreadCount = $messageModel->countUnreadForUser($user['id']);

        $this->view('employer/dashboard', [
            'employer'    => $employer,
            'placements'  => $placements,
            'messages'    => $messages,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * View + edit organisation information.
     */
    public function profile()
    {
        Session::init();
        Auth::requireRole('employer');

        $employerModel = $this->model('Employer');
        $user          = Auth::user();
        $employer      = $employerModel->findByUserId($user['id']);

        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $company_name = trim($_POST['company_name'] ?? '');
            $contact_name = trim($_POST['contact_name'] ?? '');
            $phone        = trim($_POST['phone'] ?? '');
            $address      = trim($_POST['address'] ?? '');

            $employerModel->updateProfile($user['id'], [
                'company_name' => $company_name,
                'contact_name' => $contact_name,
                'phone'        => $phone,
                'address'      => $address,
            ]);

            $success  = 'Organisation details updated successfully.';
            $employer = $employerModel->findByUserId($user['id']);
        }

        $this->view('employer/profile', [
            'employer' => $employer,
            'success'  => $success,
        ]);
    }

    /**
     * Show form to create a new placement.
     */
    public function createPlacement()
    {
        Session::init();
        Auth::requireRole('employer');

        $this->view('employer/create_placement');
    }

    /**
     * Handle form submission for new placement.
     */
    public function storePlacement()
    {
        Session::init();
        Auth::requireRole('employer');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL_ROOT . '/employer/createPlacement');
            exit;
        }

        $placementModel = $this->model('Placement');
        $employerModel  = $this->model('Employer');
        $user           = Auth::user();
        $employer       = $employerModel->findByUserId($user['id']);

        $data = [
            'title'           => trim($_POST['title'] ?? ''),
            'description'     => trim($_POST['description'] ?? ''),
            'skills_required' => trim($_POST['skills_required'] ?? ''),
            'salary'          => trim($_POST['salary'] ?? ''),
            'location'        => trim($_POST['location'] ?? ''),
            'start_date'      => $_POST['start_date'] ?? null,
            'end_date'        => $_POST['end_date'] ?? null,
        ];

        $placementModel->create($employer['id'], $data);

        header('Location: ' . URL_ROOT . '/employer/dashboard');
        exit;
    }

    /**
     * Show all placements for this employer (separate page).
     */
    public function placements()
    {
        Session::init();
        Auth::requireRole('employer');

        $employerModel  = $this->model('Employer');
        $placementModel = $this->model('Placement');
        $user           = Auth::user();
        $employer       = $employerModel->findByUserId($user['id']);
        $placements     = $placementModel->findByEmployer($employer['id']);

        $this->view('employer/placements', [
            'employer'   => $employer,
            'placements' => $placements,
        ]);
    }

    /**
     * Edit placement form.
     */
    public function editPlacement($id)
    {
        Session::init();
        Auth::requireRole('employer');

        $placementModel = $this->model('Placement');
        $employerModel  = $this->model('Employer');
        $user           = Auth::user();
        $employer       = $employerModel->findByUserId($user['id']);

        $placement = $placementModel->find($id);

        // Basic ownership check – only allow owner employer to edit
        if (!$placement || (int)$placement['employer_id'] !== (int)$employer['id']) {
            header('Location: ' . URL_ROOT . '/employer/dashboard');
            exit;
        }

        $this->view('employer/edit_placement', [
            'placement' => $placement,
        ]);
    }

    /**
     * Handle edit placement submission.
     */
    public function updatePlacement($id)
    {
        Session::init();
        Auth::requireRole('employer');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL_ROOT . '/employer/dashboard');
            exit;
        }

        $placementModel = $this->model('Placement');
        $employerModel  = $this->model('Employer');
        $user           = Auth::user();
        $employer       = $employerModel->findByUserId($user['id']);

        $data = [
            'title'           => trim($_POST['title'] ?? ''),
            'description'     => trim($_POST['description'] ?? ''),
            'skills_required' => trim($_POST['skills_required'] ?? ''),
            'salary'          => trim($_POST['salary'] ?? ''),
            'location'        => trim($_POST['location'] ?? ''),
            'start_date'      => $_POST['start_date'] ?? null,
            'end_date'        => $_POST['end_date'] ?? null,
        ];

        $placementModel->update($id, $employer['id'], $data);

        header('Location: ' . URL_ROOT . '/employer/dashboard');
        exit;
    }

    /**
     * Delete a placement.
     */
    public function deletePlacement($id)
    {
        Session::init();
        Auth::requireRole('employer');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL_ROOT . '/employer/dashboard');
            exit;
        }

        $placementModel = $this->model('Placement');
        $employerModel  = $this->model('Employer');
        $user           = Auth::user();
        $employer       = $employerModel->findByUserId($user['id']);

        $placementModel->delete($id, $employer['id']);

        header('Location: ' . URL_ROOT . '/employer/dashboard');
        exit;
    }

    /**
     * View a single message and mark it as read.
     * URL: /employer/message/{id}
     */
    public function message($id)
    {
        Session::init();
        Auth::requireRole('employer');

        $messageModel = $this->model('Message');
        $user         = Auth::user();

        // Ensure message belongs to this employer's user account
        $message = $messageModel->findForUser($id, $user['id']);
        if (!$message) {
            header('Location: ' . URL_ROOT . '/employer/dashboard');
            exit;
        }

        // Mark as read
        $messageModel->markAsRead($id, $user['id']);

        $this->view('message/view', [
            'message'      => $message,
            'dashboardUrl' => URL_ROOT . '/employer/dashboard',
        ]);
    }

    /**
     * View applicants for a specific placement.
     * URL: /employer/applicants/{placementId}
     */
    public function applicants($placementId)
    {
        Session::init();
        Auth::requireRole('employer');

        $placementId      = (int)$placementId;
        $placementModel   = $this->model('Placement');
        $employerModel    = $this->model('Employer');
        $applicationModel = $this->model('Application');

        $user            = Auth::user();
        $employerProfile = $employerModel->findByUserId($user['id']);

        if (!$employerProfile) {
            header('Location: ' . URL_ROOT . '/employer/dashboard');
            exit;
        }

        $placement = $placementModel->find($placementId);
        if (!$placement || (int)$placement['employer_id'] !== (int)$employerProfile['id']) {
            // placement does not belong to this employer
            header('Location: ' . URL_ROOT . '/employer/dashboard');
            exit;
        }

        $applicants = $applicationModel->getApplicantsForPlacement($placementId);

        $this->view('employer/applicants', [
            'placement'  => $placement,
            'applicants' => $applicants,
        ]);
    }
}
