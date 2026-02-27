<?php

class MessageController extends Controller
{
    private $messageModel;
    private $user;

    public function __construct()
    {
        Session::init();
        $this->user = Session::get('user');

        if (!$this->user) {
            header('Location: ' . URL_ROOT . '/auth/login');
            exit;
        }

        $this->messageModel = $this->model('Message');
    }

    /**
     * Generic inbox for the logged-in user.
     */
    public function inbox()
    {
        $messages    = $this->messageModel->getForUser($this->user['id']);
        $unreadCount = $this->messageModel->countUnreadForUser($this->user['id']);

        $this->view('message/inbox', [
            'messages'    => $messages,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * View a single message (uses your existing view.php).
     */
    public function view($id)
    {
        $message = $this->messageModel->findForUser((int)$id, $this->user['id']);

        if (!$message) {
            header('Location: ' . URL_ROOT . '/message/inbox');
            exit;
        }

        if (!$message['is_read']) {
            $this->messageModel->markAsRead((int)$id, $this->user['id']);
            $message['is_read'] = 1;
        }

        // Decide where "Close" goes based on role
        $dashboardUrl = URL_ROOT . '/';
        if ($this->user['role'] === 'student') {
            $dashboardUrl = URL_ROOT . '/student/dashboard';
        } elseif ($this->user['role'] === 'employer') {
            $dashboardUrl = URL_ROOT . '/employer/dashboard';
        } elseif ($this->user['role'] === 'careers') {
            $dashboardUrl = URL_ROOT . '/admin'; // or a careers dashboard if you have one
        }

        $this->view('message/view', [
            'message'      => $message,
            'dashboardUrl' => $dashboardUrl,
        ]);
    }

    /**
     * Chat between a STUDENT and the default CAREER SUPPORT user.
     * Route example: /message/studentChat
     */
    public function studentChat()
    {
        if ($this->user['role'] !== 'student') {
            header('Location: ' . URL_ROOT . '/');
            exit;
        }

        $careerUser = $this->getDefaultCareerSupportUser();
        if (!$careerUser) {
            die('No career support user exists yet.');
        }

        $studentId   = $this->user['id'];
        $careerId    = $careerUser['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $body = trim($_POST['body'] ?? '');
            if ($body !== '') {
                $this->messageModel->send(
                    $careerId,
                    'Student message',
                    $body,
                    $studentId
                );
            }

            // POST-redirect-GET
            header('Location: ' . URL_ROOT . '/message/studentChat');
            exit;
        }

        $this->messageModel->markConversationRead($studentId, $careerId);

        $conversation = $this->messageModel->getConversationBetween($studentId, $careerId);

        $this->view('message/chat', [
            'conversation' => $conversation,
            'otherUser'    => $careerUser,
            'currentUser'  => $this->user,
            'backUrl'      => URL_ROOT . '/student/dashboard',
        ]);
    }

    /**
     * Chat between CAREER SUPPORT and a specific student.
     * Route example: /message/careerChat/{studentId}
     */
    public function careerChat($studentId)
    {
        if ($this->user['role'] !== 'careers') {
            header('Location: ' . URL_ROOT . '/');
            exit;
        }

        $studentId = (int)$studentId;

        $studentModel = $this->model('Student');
        $student      = $studentModel->getUserById($studentId); // adjust if your method name differs

        if (!$student) {
            die('Student not found');
        }

        $careerId = $this->user['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $body = trim($_POST['body'] ?? '');
            if ($body !== '') {
                $this->messageModel->send(
                    $studentId,
                    'Career support message',
                    $body,
                    $careerId
                );
            }

            header('Location: ' . URL_ROOT . '/message/careerChat/' . $studentId);
            exit;
        }

        $this->messageModel->markConversationRead($careerId, $studentId);

        $conversation = $this->messageModel->getConversationBetween($careerId, $studentId);

        $this->view('message/chat', [
            'conversation' => $conversation,
            'otherUser'    => ['id' => $studentId, 'name' => $student['name']],
            'currentUser'  => $this->user,
            'backUrl'      => URL_ROOT . '/admin', // or a careers dashboard
        ]);
    }

    /**
     * Helper: get the first user with role = 'careers'.
     */
    private function getDefaultCareerSupportUser()
    {
        $userModel = $this->model('User');

        return $userModel->getFirstByRole('careers');
    }
}
