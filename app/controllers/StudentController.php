<?php

require_once APP_ROOT . '/app/core/Controller.php';
require_once APP_ROOT . '/app/core/Session.php';
require_once APP_ROOT . '/app/core/Auth.php';
require_once APP_ROOT . '/app/core/Database.php';

class StudentController extends Controller
{
    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        Session::init();
        Auth::requireRole('student');

        $studentModel   = $this->model('Student');
        $placementModel = $this->model('Placement');
        $matchModel     = $this->model('MatchModel');
        $messageModel   = $this->model('Message');
        $userModel      = $this->model('User');

        $user           = Auth::user();
        $studentProfile = $studentModel->findByUserId($user['id']);

        if (!$studentProfile) {
            $this->view('student/profile_missing', [
                'user' => $user
            ]);
            return;
        }

        $placements  = $placementModel->all();
        $matches     = $matchModel->getMatchesForStudent($studentProfile['id']);
        $messages    = $messageModel->getForUser($user['id']);
        $unreadCount = $messageModel->countUnreadForUser($user['id']);

        $careerChatCount = 0;
        $careerUser      = $userModel->getFirstByRole('admin');

        if ($careerUser && method_exists($messageModel, 'getConversationBetween')) {
            $conversation    = $messageModel->getConversationBetween($user['id'], $careerUser['id']);
            $careerChatCount = is_array($conversation) ? count($conversation) : 0;
        }

        $this->view('student/dashboard', [
            'student'         => $studentProfile,
            'placements'      => $placements,
            'matches'         => $matches,
            'messages'        => $messages,
            'unreadCount'     => $unreadCount,
            'careerChatCount' => $careerChatCount,
        ]);
    }

    public function profile()
    {
        Session::init();
        Auth::requireRole('student');

        $studentModel   = $this->model('Student');
        $user           = Auth::user();
        $studentProfile = $studentModel->findByUserId($user['id']);
        $success        = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name    = trim($_POST['name'] ?? '');
            $phone   = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $skills  = trim($_POST['skills'] ?? '');

            $studentModel->updateProfile($user['id'], [
                'name'    => $name,
                'phone'   => $phone,
                'address' => $address,
                'skills'  => $skills
            ]);

            $studentProfile = $studentModel->findByUserId($user['id']);

            require_once APP_ROOT . '/app/controllers/MatchController.php';
            $matchController = new MatchController();
            $matchController->regenerateForStudent($studentProfile['id']);

            $success = 'Profile updated and matches refreshed.';
        }

        $this->view('student/profile', [
            'student' => $studentProfile,
            'success' => $success
        ]);
    }

    public function uploadCv()
    {
        Session::init();
        Auth::requireRole('student');

        $studentModel   = $this->model('Student');
        $user           = Auth::user();
        $studentProfile = $studentModel->findByUserId($user['id']);

        $error   = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cv'])) {
            $file = $_FILES['cv'];

            if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                $error = 'File upload error.';
            } else {
                // Limit size (3MB)
                $maxBytes = 3 * 1024 * 1024;
                $size = (int)($file['size'] ?? 0);

                if ($size <= 0 || $size > $maxBytes) {
                    $error = 'CV must be a PDF under 3MB.';
                } else {
                    // Validate MIME type using finfo (server-side)
                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                    $mime  = $finfo->file($file['tmp_name']);

                    if ($mime !== 'application/pdf') {
                        $error = 'Only PDF files are allowed.';
                    } else {
                        // Signature check: first 4 bytes must be "%PDF"
                        $fh = fopen($file['tmp_name'], 'rb');
                        $sig = $fh ? fread($fh, 4) : '';
                        if ($fh) fclose($fh);

                        if ($sig !== '%PDF') {
                            $error = 'Invalid PDF file.';
                        } else {
                            $targetDir = APP_ROOT . '/storage/cv';

                            if (!is_dir($targetDir)) {
                                mkdir($targetDir, 0755, true);
                            }

                            // Delete old CV if present
                            if (!empty($studentProfile['cv_filename'])) {
                                $old = $targetDir . '/' . basename($studentProfile['cv_filename']);
                                if (is_file($old)) {
                                    @unlink($old);
                                }
                            }

                            $random = bin2hex(random_bytes(16));
                            $filename   = 'student_' . (int)$studentProfile['id'] . '_' . $random . '.pdf';
                            $targetPath = $targetDir . '/' . $filename;

                            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                                @chmod($targetPath, 0644);

                                $studentModel->updateCv($user['id'], $filename);
                                $success        = 'CV uploaded successfully.';
                                $studentProfile = $studentModel->findByUserId($user['id']);
                            } else {
                                $error = 'Failed to store uploaded file.';
                            }
                        }
                    }
                }
            }
        }

        $this->view('student/upload_cv', [
            'student' => $studentProfile,
            'error'   => $error,
            'success' => $success
        ]);
    }

    public function matches()
    {
        Session::init();
        Auth::requireRole('student');

        $studentModel     = $this->model('Student');
        $matchModel       = $this->model('MatchModel');
        $applicationModel = $this->model('Application');

        $user           = Auth::user();
        $studentProfile = $studentModel->findByUserId($user['id']);
        $matches        = $matchModel->getMatchesForStudent($studentProfile['id']);

        $applicationsByPlacement = $applicationModel->getApplicationsForStudentIndexed($studentProfile['id']);

        $this->view('student/matches', [
            'student'                 => $studentProfile,
            'matches'                 => $matches,
            'applicationsByPlacement' => $applicationsByPlacement,
        ]);
    }

    public function apply($placementId)
    {
        Session::init();
        Auth::requireRole('student');

        $placementId = (int)$placementId;

        $studentModel     = $this->model('Student');
        $applicationModel = $this->model('Application');
        $placementModel   = $this->model('Placement');

        $user           = Auth::user();
        $studentProfile = $studentModel->findByUserId($user['id']);

        if (!$studentProfile) {
            header('Location: ' . URL_ROOT . '/student/dashboard');
            exit;
        }

        $placement = $placementModel->find($placementId);
        if (!$placement || (isset($placement['status']) && $placement['status'] !== 'approved')) {
            header('Location: ' . URL_ROOT . '/student/matches');
            exit;
        }

        $applicationModel->apply($placementId, $studentProfile['id']);

        header('Location: ' . URL_ROOT . '/student/matches');
        exit;
    }

    public function message($id)
    {
        Session::init();
        Auth::requireRole('student');

        $messageModel = $this->model('Message');
        $user         = Auth::user();

        $message = $messageModel->findForUser($id, $user['id']);
        if (!$message) {
            header('Location: ' . URL_ROOT . '/student/dashboard');
            exit;
        }

        $messageModel->markAsRead($id, $user['id']);

        $this->view('message/view', [
            'message'      => $message,
            'dashboardUrl' => URL_ROOT . '/student/dashboard',
        ]);
    }

    public function chatCareerSupport()
    {
        Session::init();
        Auth::requireRole('student');

        $user      = Auth::user();
        $userModel = $this->model('User');
        $msgModel  = $this->model('Message');

        $careerUser = $userModel->getFirstByRole('admin');

        if (!$careerUser) {
            $this->view('message/chat', [
                'conversation' => [],
                'otherUser'    => ['name' => 'Career Support'],
                'currentUser'  => $user,
                'backUrl'      => URL_ROOT . '/student/dashboard',
                'error'        => 'Career support is not configured yet. Please ask an administrator.'
            ]);
            return;
        }

        $studentId = (int)$user['id'];
        $careerId  = (int)$careerUser['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $body = trim($_POST['body'] ?? '');
            if ($body !== '') {
                $msgModel->send(
                    $careerId,
                    'Message from student',
                    $body,
                    $studentId
                );
            }

            header('Location: ' . URL_ROOT . '/student/chatCareerSupport');
            exit;
        }

        if (method_exists($msgModel, 'markConversationRead')) {
            $msgModel->markConversationRead($studentId, $careerId);
        }

        if (method_exists($msgModel, 'getConversationBetween')) {
            $conversation = $msgModel->getConversationBetween($studentId, $careerId);
        } else {
            $conversation = [];
        }

        $this->view('message/chat', [
            'conversation' => $conversation,
            'otherUser'    => $careerUser,
            'currentUser'  => $user,
            'backUrl'      => URL_ROOT . '/student/dashboard',
        ]);
    }
}