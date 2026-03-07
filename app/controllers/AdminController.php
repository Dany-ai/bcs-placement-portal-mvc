<?php

require_once APP_ROOT . '/app/core/Controller.php';
require_once APP_ROOT . '/app/core/Session.php';
require_once APP_ROOT . '/app/core/Auth.php';
require_once APP_ROOT . '/app/core/Database.php';

class AdminController extends Controller
{
    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        Session::init();
        Auth::requireRole('admin');

        $placementModel = $this->model('Placement');
        $studentModel   = $this->model('Student');
        $messageModel   = $this->model('Message');
        $adminUser      = Auth::user();

        $pendingPlacements = $placementModel->findPending();
        $searchTerm = trim($_GET['q'] ?? '');

        if ($searchTerm !== '' && method_exists($studentModel, 'searchWithUser')) {
            $students = $studentModel->searchWithUser($searchTerm);
        } else {
            $students = $studentModel->allWithUser();
        }

        foreach ($students as &$s) {
            $unread = $messageModel->countUnreadFromUserToUser(
                $s['user_id'],
                $adminUser['id']
            );
            $s['unread_from_student'] = $unread;
        }
        unset($s);

        $this->view('admin/dashboard', [
            'pendingPlacements' => $pendingPlacements,
            'students'          => $students,
            'searchTerm'        => $searchTerm,
        ]);
    }

    public function approvePlacement($id)
    {
        Session::init();
        Auth::requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/dashboard');
        }

        $this->requireCsrf();

        $placementModel = $this->model('Placement');
        $messageModel   = $this->model('Message');
        $adminUser      = Auth::user();

        $placement = $placementModel->find($id);
        if (!$placement) {
            $this->flash('error', 'Placement not found.');
            $this->redirect('/admin/dashboard');
        }

        $placementModel->setStatus($id, 'approved', $adminUser['id']);

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT u.id AS user_id, e.company_name
            FROM employers e
            JOIN users u ON u.id = e.user_id
            WHERE e.id = :employer_id
            LIMIT 1
        ");
        $stmt->execute([':employer_id' => $placement['employer_id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $subject = 'Placement approved';
            $body = "Your placement \"{$placement['title']}\" has been approved by the admin and is now visible to students.";
            $messageModel->send($row['user_id'], $subject, $body, $adminUser['id']);
        }

        $this->flash('success', 'Placement approved.');
        $this->redirect('/admin/dashboard');
    }

    public function rejectPlacement($id)
    {
        Session::init();
        Auth::requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/dashboard');
        }

        $this->requireCsrf();

        $placementModel = $this->model('Placement');
        $messageModel   = $this->model('Message');
        $adminUser      = Auth::user();

        $placement = $placementModel->find($id);
        if (!$placement) {
            $this->flash('error', 'Placement not found.');
            $this->redirect('/admin/dashboard');
        }

        $placementModel->setStatus($id, 'rejected', $adminUser['id']);

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT u.id AS user_id, e.company_name
            FROM employers e
            JOIN users u ON u.id = e.user_id
            WHERE e.id = :employer_id
            LIMIT 1
        ");
        $stmt->execute([':employer_id' => $placement['employer_id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $subject = 'Placement not approved';
            $body    = "Your placement \"{$placement['title']}\" has been reviewed and was not approved at this time.\n\n" .
                       "You may edit the placement and resubmit if appropriate.";
            $messageModel->send($row['user_id'], $subject, $body, $adminUser['id']);
        }

        $this->flash('success', 'Placement rejected.');
        $this->redirect('/admin/dashboard');
    }

    public function chatStudent($studentUserId)
    {
        Session::init();
        Auth::requireRole('admin');

        $adminUser     = Auth::user();
        $msgModel      = $this->model('Message');
        $studentModel  = $this->model('Student');

        $studentUserId = (int)$studentUserId;
        $studentProfile = $studentModel->findByUserId($studentUserId);

        if (!$studentProfile) {
            $this->redirect('/admin/dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireCsrf();

            $body = trim($_POST['body'] ?? '');
            if ($body !== '') {
                $msgModel->send(
                    $studentUserId,
                    'Career support message',
                    $body,
                    $adminUser['id']
                );
            }

            $this->redirect('/admin/chatStudent/' . $studentUserId);
        }

        if (method_exists($msgModel, 'markConversationRead')) {
            $msgModel->markConversationRead($adminUser['id'], $studentUserId);
        }

        $conversation = method_exists($msgModel, 'getConversationBetween')
            ? $msgModel->getConversationBetween($adminUser['id'], $studentUserId)
            : [];

        $this->view('message/chat', [
            'conversation' => $conversation,
            'otherUser'    => [
                'id'   => $studentUserId,
                'name' => $studentProfile['name'] ?? 'Student'
            ],
            'currentUser'  => $adminUser,
            'backUrl'      => URL_ROOT . '/admin/dashboard',
        ]);
    }
}