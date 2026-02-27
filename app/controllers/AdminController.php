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

    /**
     * Admin dashboard: show pending placements + students for chat.
     */
    public function dashboard()
    {
        Session::init();
        Auth::requireRole('admin');

        $placementModel = $this->model('Placement');
        $studentModel   = $this->model('Student');
        $messageModel   = $this->model('Message');
        $adminUser      = Auth::user();

        $pendingPlacements = $placementModel->findPending();

        // Search term from query string (?q=...)
        $searchTerm = trim($_GET['q'] ?? '');

        if ($searchTerm !== '' && method_exists($studentModel, 'searchWithUser')) {
            $students = $studentModel->searchWithUser($searchTerm);
        } else {
            $students = $studentModel->allWithUser();
        }

        // For each student, compute unread messages from that student -> this admin
        foreach ($students as &$s) {
            $unread = $messageModel->countUnreadFromUserToUser(
                $s['user_id'],          // sender: student
                $adminUser['id']        // recipient: admin
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

    /**
     * Approve a placement.
     * URL: /admin/approvePlacement/{id}
     */
    public function approvePlacement($id)
    {
        Session::init();
        Auth::requireRole('admin');

        $placementModel = $this->model('Placement');
        $messageModel   = $this->model('Message');

        $adminUser = Auth::user();

        // Get placement + employer info
        $placement = $placementModel->find($id);
        if (!$placement) {
            header('Location: ' . URL_ROOT . '/admin/dashboard');
            exit;
        }

        // Update status to approved
        $placementModel->setStatus($id, 'approved', $adminUser['id']);

        // Find employer's user ID via join
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
            $body    = "Your placement \"{$placement['title']}\" has been approved by the admin and is now visible to students.";
            $messageModel->send($row['user_id'], $subject, $body, $adminUser['id']);
        }

        header('Location: ' . URL_ROOT . '/admin/dashboard');
        exit;
    }

    /**
     * Reject a placement.
     * URL: /admin/rejectPlacement/{id}
     */
    public function rejectPlacement($id)
    {
        Session::init();
        Auth::requireRole('admin');

        $placementModel = $this->model('Placement');
        $messageModel   = $this->model('Message');

        $adminUser = Auth::user();

        $placement = $placementModel->find($id);
        if (!$placement) {
            header('Location: ' . URL_ROOT . '/admin/dashboard');
            exit;
        }

        // Update status to rejected
        $placementModel->setStatus($id, 'rejected', $adminUser['id']);

        // Find employer's user ID
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
            $body    = "Your placement \"{$placement['title']}\" has been reviewed and was not approved at this time.\n\n".
                "You may edit the placement and resubmit if appropriate.";
            $messageModel->send($row['user_id'], $subject, $body, $adminUser['id']);
        }

        header('Location: ' . URL_ROOT . '/admin/dashboard');
        exit;
    }

    /**
     * Career support/admin chat with a specific student.
     * URL: /admin/chatStudent/{studentUserId}
     */
    public function chatStudent($studentUserId)
    {
        Session::init();
        Auth::requireRole('admin');

        $adminUser     = Auth::user();          // current admin / career support user
        $msgModel      = $this->model('Message');
        $studentModel  = $this->model('Student');

        $studentUserId = (int)$studentUserId;

        // Student profile via user_id
        $studentProfile = $studentModel->findByUserId($studentUserId);

        if (!$studentProfile) {
            header('Location: ' . URL_ROOT . '/admin/dashboard');
            exit;
        }

        // Handle new message POST from admin to student
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $body = trim($_POST['body'] ?? '');
            if ($body !== '') {
                $msgModel->send(
                    $studentUserId,                  // recipient_user_id
                    'Career support message',        // subject
                    $body,                           // body
                    $adminUser['id']                 // sender_user_id
                );
            }

            header('Location: ' . URL_ROOT . '/admin/chatStudent/' . $studentUserId);
            exit;
        }

        // Mark all messages from this student to the admin as read
        if (method_exists($msgModel, 'markConversationRead')) {
            $msgModel->markConversationRead($adminUser['id'], $studentUserId);
        }

        // Get full conversation (both directions)
        if (method_exists($msgModel, 'getConversationBetween')) {
            $conversation = $msgModel->getConversationBetween($adminUser['id'], $studentUserId);
        } else {
            $conversation = [];
        }

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
