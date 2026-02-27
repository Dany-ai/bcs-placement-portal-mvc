<?php

class Controller
{
    public function model($model)
    {
        require_once APP_ROOT . '/app/models/' . $model . '.php';
        return new $model();
    }

    /**
     * Flash helpers (stored in session for next request only)
     */
    protected function flash(string $type, string $message): void
    {
        Session::init();
        Session::set('_flash', [
            'type' => $type,
            'message' => $message,
        ]);
    }

    protected function getFlash(): ?array
    {
        Session::init();
        $flash = Session::get('_flash');
        Session::remove('_flash');
        return is_array($flash) ? $flash : null;
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . URL_ROOT . $path);
        exit;
    }

    /**
     * CSRF helpers
     */
    protected function csrfField(): string
    {
        $token = htmlspecialchars(Session::csrfToken(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="_csrf" value="' . $token . '">';
    }

    protected function requireCsrf(): void
    {
        Session::init();
        $token = $_POST['_csrf'] ?? null;
        if (!Session::verifyCsrf($token)) {
            http_response_code(419);
            die('Invalid CSRF token.');
        }
    }

    public function view($view, $data = [])
    {
        Session::init();

        // Make these available in all views
        $data['_currentUser'] = Auth::user();
        $data['_flash'] = $this->getFlash();
        $data['_csrfField'] = $this->csrfField();

        extract($data);

        require APP_ROOT . '/app/views/layouts/header.php';
        require APP_ROOT . '/app/views/layouts/navbar.php';
        require APP_ROOT . '/app/views/' . $view . '.php';
        require APP_ROOT . '/app/views/layouts/footer.php';
    }
}