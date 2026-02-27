<?php

require_once APP_ROOT . '/app/core/Controller.php';
require_once APP_ROOT . '/app/core/Session.php';
require_once APP_ROOT . '/app/core/Auth.php';
require_once APP_ROOT . '/app/core/Database.php';

class HomeController extends Controller
{
    public function index()
    {
        Session::init();
        $placementModel = $this->model('Placement');
        $placements = $placementModel->all();

        $this->view('home', [
            'placements' => $placements
        ]);
    }

    public function benefits()
    {
        Session::init();
        $this->view('pages/benefits');
    }
}
