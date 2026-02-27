<?php

class Controller
{
    public function model($model)
    {
        require_once APP_ROOT . '/app/models/' . $model . '.php';
        return new $model();
    }

    public function view($view, $data = [])
    {
        extract($data);
        require APP_ROOT . '/app/views/layouts/header.php';
        require APP_ROOT . '/app/views/layouts/navbar.php';
        require APP_ROOT . '/app/views/' . $view . '.php';
        require APP_ROOT . '/app/views/layouts/footer.php';
    }
}
