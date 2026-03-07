<?php

require_once APP_ROOT . '/app/core/Controller.php';
require_once APP_ROOT . '/app/core/Session.php';
require_once APP_ROOT . '/app/core/Auth.php';
require_once APP_ROOT . '/app/core/Database.php';

class EmployerController extends Controller
{
    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        Session::init();
        Auth::requireRole('employer');

        $employerModel  = $this->model('Employer');
        $placementModel = $this->model('Placement');
        $messageModel   = $this->model('Message');

        $user       = Auth::user();
        $employer   = $employerModel->findByUserId($user['id']);
        $placements = $employer ? $placementModel->findByEmployer($employer['id']) : [];

        $messages    = $messageModel->getForUser($user['id']);
        $unreadCount = $messageModel->countUnreadForUser($user['id']);

        $this->view('employer/dashboard', [
            'employer'    => $employer,
            'placements'  => $placements,
            'messages'    => $messages,
            'unreadCount' => $unreadCount,
        ]);
    }

    public function profile()
    {
        Session::init();
        Auth::requireRole('employer');

        $employerModel = $this->model('Employer');
        $user          = Auth::user();
        $employer      = $employerModel->findByUserId($user['id']);

        $success = '';
        $error   = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireCsrf();

            $company_name = trim($_POST['company_name'] ?? '');
            $contact_name = trim($_POST['contact_name'] ?? '');
            $phone        = trim($_POST['phone'] ?? '');
            $address      = trim($_POST['address'] ?? '');

            if ($company_name === '') {
                $error = 'Company name is required.';
            } else {
                $employerModel->updateProfile($user['id'], [
                    'company_name' => $company_name,
                    'contact_name' => $contact_name,
                    'phone'        => $phone,
                    'address'      => $address,
                ]);

                $success  = 'Organisation details updated successfully.';
                $employer = $employerModel->findByUserId($user['id']);
            }
        }

        $this->view('employer/profile', [
            'employer' => $employer,
            'success'  => $success,
            'error'    => $error,
        ]);
    }

    public function createPlacement()
    {
        Session::init();
        Auth::requireRole('employer');

        $this->view('employer/create_placement');
    }

    public function storePlacement()
    {
        Session::init();
        Auth::requireRole('employer');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/employer/createPlacement');
        }

        $this->requireCsrf();

        $placementModel = $this->model('Placement');
        $employerModel  = $this->model('Employer');
        $user           = Auth::user();
        $employer       = $employerModel->findByUserId($user['id']);

        if (!$employer) {
            $this->flash('error', 'Employer profile not found.');
            $this->redirect('/employer/dashboard');
        }

        $data = [
            'title'           => trim($_POST['title'] ?? ''),
            'description'     => trim($_POST['description'] ?? ''),
            'skills_required' => trim($_POST['skills_required'] ?? ''),
            'salary'          => trim($_POST['salary'] ?? ''),
            'location'        => trim($_POST['location'] ?? ''),
            'start_date'      => $_POST['start_date'] ?? null,
            'end_date'        => $_POST['end_date'] ?? null,
        ];

        if ($data['title'] === '' || $data['description'] === '') {
            $this->flash('error', 'Title and description are required.');
            $this->redirect('/employer/createPlacement');
        }

        $placementModel->create($employer['id'], $data);

        $this->flash('success', 'Placement created and submitted for review.');
        $this->redirect('/employer/dashboard');
    }

    public function placements()
    {
        Session::init();
        Auth::requireRole('employer');

        $employerModel  = $this->model('Employer');
        $placementModel = $this->model('Placement');
        $user           = Auth::user();
        $employer       = $employerModel->findByUserId($user['id']);
        $placements     = $employer ? $placementModel->findByEmployer($employer['id']) : [];

        $this->view('employer/placements', [
            'employer'   => $employer,
            'placements' => $placements,
        ]);
    }

    public function editPlacement($id)
    {
        Session::init();
        Auth::requireRole('employer');

        $placementModel = $this->model('Placement');
        $employerModel  = $this->model('Employer');
        $user           = Auth::user();
        $employer       = $employerModel->findByUserId($user['id']);

        $placement = $placementModel->find($id);

        if (!$placement || !$employer || (int)$placement['employer_id'] !== (int)$employer['id']) {
            $this->redirect('/employer/dashboard');
        }

        $this->view('employer/edit_placement', [
            'placement' => $placement,
        ]);
    }

    public function updatePlacement($id)
    {
        Session::init();
        Auth::requireRole('employer');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/employer/dashboard');
        }

        $this->requireCsrf();

        $placementModel = $this->model('Placement');
        $employerModel  = $this->model('Employer');
        $user           = Auth::user();
        $employer       = $employerModel->findByUserId($user['id']);

        if (!$employer) {
            $this->flash('error', 'Employer profile not found.');
            $this->redirect('/employer/dashboard');
        }

        $data = [
            'title'           => trim($_POST['title'] ?? ''),
            'description'     => trim($_POST['description'] ?? ''),
            'skills_required' => trim($_POST['skills_required'] ?? ''),
            'salary'          => trim($_POST['salary'] ?? ''),
            'location'        => trim($_POST['location'] ?? ''),
            'start_date'      => $_POST['start_date'] ?? null,
            'end_date'        => $_POST['end_date'] ?? null,
        ];

        if ($data['title'] === '' || $data['description'] === '') {
            $this->flash('error', 'Title and description are required.');
            $this->redirect('/employer/dashboard');
        }

        $placementModel->update($id, $employer['id'], $data);

        $this->flash('success', 'Placement updated successfully.');
        $this->redirect('/employer/dashboard');
    }

    public function deletePlacement($id)
    {
        Session::init();
        Auth::requireRole('employer');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/employer/dashboard');
        }

        $this->requireCsrf();

        $placementModel = $this->model('Placement');
        $employerModel  = $this->model('Employer');
        $user           = Auth::user();
        $employer       = $employerModel->findByUserId($user['id']);

        if (!$employer) {
            $this->flash('error', 'Employer profile not found.');
            $this->redirect('/employer/dashboard');
        }

        $placementModel->delete($id, $employer['id']);

        $this->flash('success', 'Placement deleted.');
        $this->redirect('/employer/dashboard');
    }

    public function message($id)
    {
        Session::init();
        Auth::requireRole('employer');

        $messageModel = $this->model('Message');
        $user         = Auth::user();

        $message = $messageModel->findForUser($id, $user['id']);
        if (!$message) {
            $this->redirect('/employer/dashboard');
        }

        $messageModel->markAsRead($id, $user['id']);

        $this->view('message/view', [
            'message'      => $message,
            'dashboardUrl' => URL_ROOT . '/employer/dashboard',
        ]);
    }

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
            $this->redirect('/employer/dashboard');
        }

        $placement = $placementModel->find($placementId);
        if (!$placement || (int)$placement['employer_id'] !== (int)$employerProfile['id']) {
            $this->redirect('/employer/dashboard');
        }

        $applicants = $applicationModel->getApplicantsForPlacement($placementId);

        $this->view('employer/applicants', [
            'placement'  => $placement,
            'applicants' => $applicants,
        ]);
    }
}