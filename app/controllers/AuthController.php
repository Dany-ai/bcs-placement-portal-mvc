<?php

require_once APP_ROOT . '/app/core/Controller.php';
require_once APP_ROOT . '/app/core/Session.php';
require_once APP_ROOT . '/app/core/Auth.php';
require_once APP_ROOT . '/app/core/Database.php';

class AuthController extends Controller
{
    private function legacyHashPassword($password)
    {
        return hash('sha256', $password);
    }

    private function isLegacySha256Hash($hash)
    {
        return is_string($hash) && (bool)preg_match('/^[a-f0-9]{64}$/i', $hash);
    }

    public function index()
    {
        $this->login();
    }

    public function login()
    {
        Session::init();
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireCsrf();

            $email    = trim($_POST['email'] ?? '');
            $password = (string)($_POST['password'] ?? '');

            $userModel = $this->model('User');
            $user      = $userModel->findByEmail($email);

            if ($user) {
                $stored = $user['password'] ?? '';
                $ok = false;

                if ($this->isLegacySha256Hash($stored)) {
                    $ok = ($this->legacyHashPassword($password) === $stored);

                    if ($ok) {
                        $newHash = password_hash($password, PASSWORD_DEFAULT);
                        $userModel->updatePasswordHash((int)$user['id'], $newHash);
                    }
                } else {
                    $ok = password_verify($password, (string)$stored);
                }

                if ($ok) {
                    Auth::login($user);

                    $role = $user['role'] ?? '';
                    if ($role === 'student') {
                        $this->redirect('/student/dashboard');
                    } elseif ($role === 'employer') {
                        $this->redirect('/employer/dashboard');
                    } elseif ($role === 'admin') {
                        $this->redirect('/admin/dashboard');
                    } else {
                        $this->redirect('/');
                    }
                }
            }

            $error = 'Invalid email or password.';
        }

        $this->view('auth/login', ['error' => $error]);
    }

    public function logout()
    {
        Session::init();
        Auth::logout();
        $this->redirect('/');
    }

    public function registerStudent()
    {
        Session::init();
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireCsrf();

            $email    = trim($_POST['email'] ?? '');
            $password = (string)($_POST['password'] ?? '');
            $name     = trim($_POST['name'] ?? '');
            $phone    = trim($_POST['phone'] ?? '');
            $address  = trim($_POST['address'] ?? '');
            $skills   = trim($_POST['skills'] ?? '');

            if ($email && $password && $name) {
                $userModel = $this->model('User');

                if ($userModel->findByEmail($email)) {
                    $error = 'Email is already registered.';
                } else {
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                    $userId = $userModel->create($email, $passwordHash, 'student', $name);

                    $studentModel = $this->model('Student');
                    $studentModel->createProfile($userId, [
                        'name'        => $name,
                        'phone'       => $phone,
                        'address'     => $address,
                        'skills'      => $skills,
                        'cv_filename' => null,
                    ]);

                    $messageModel = $this->model('Message');
                    $subject = 'Welcome to the BCS Placement Portal';
                    $body    = "Hi {$name},\n\n".
                        "Your student account has been created successfully. ".
                        "You can now log in, update your profile, upload your CV and view your placement matches.\n\n".
                        "Regards,\nBCS Placement Portal";
                    $messageModel->send($userId, $subject, $body, null);

                    $this->redirect('/auth/login');
                }
            } else {
                $error = 'Please fill in the required fields.';
            }
        }

        $this->view('auth/register_student', ['error' => $error]);
    }

    public function registerEmployer()
    {
        Session::init();
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireCsrf();

            $email        = trim($_POST['email'] ?? '');
            $password     = (string)($_POST['password'] ?? '');
            $company_name = trim($_POST['company_name'] ?? '');
            $contact_name = trim($_POST['contact_name'] ?? '');
            $phone        = trim($_POST['phone'] ?? '');
            $address      = trim($_POST['address'] ?? '');

            if ($email && $password && $company_name) {
                $userModel = $this->model('User');

                if ($userModel->findByEmail($email)) {
                    $error = 'Email is already registered.';
                } else {
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                    $displayName = $company_name ?: $contact_name;
                    $userId = $userModel->create($email, $passwordHash, 'employer', $displayName);

                    $employerModel = $this->model('Employer');
                    $employerModel->createProfile($userId, [
                        'company_name' => $company_name,
                        'contact_name' => $contact_name,
                        'phone'        => $phone,
                        'address'      => $address,
                    ]);

                    $messageModel = $this->model('Message');
                    $subject = 'Welcome to the BCS Placement Portal';
                    $body    = "Hi {$contact_name},\n\n".
                        "Your employer account for {$company_name} has been created successfully. ".
                        "You can now log in, manage your organisation profile and publish placements for students.\n\n".
                        "Regards,\nBCS Placement Portal";
                    $messageModel->send($userId, $subject, $body, null);

                    $this->redirect('/auth/login');
                }
            } else {
                $error = 'Please fill in the required fields.';
            }
        }

        $this->view('auth/register_employer', ['error' => $error]);
    }

    public function registerCareer()
    {
        Session::init();
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireCsrf();

            $email    = trim($_POST['email'] ?? '');
            $password = (string)($_POST['password'] ?? '');
            $name     = trim($_POST['name'] ?? '');

            if ($email && $password) {
                $userModel = $this->model('User');

                if ($userModel->findByEmail($email)) {
                    $error = 'Email is already registered.';
                } else {
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                    $userId = $userModel->create($email, $passwordHash, 'admin', $name);

                    $messageModel = $this->model('Message');
                    $subject = 'Welcome to the BCS Placement Portal (Career Support)';
                    $body    = "Hello {$name},\n\n".
                        "Your career support/admin account has been created successfully. ".
                        "You can now log in and support students by answering their questions, ".
                        "giving feedback on CVs and helping them with placements.\n\n".
                        "Regards,\nBCS Placement Portal";
                    $messageModel->send($userId, $subject, $body, null);

                    $this->redirect('/auth/login');
                }
            } else {
                $error = 'Please fill in the required fields.';
            }
        }

        $this->view('auth/register_career', ['error' => $error]);
    }
}