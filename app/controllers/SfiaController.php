<?php

require_once APP_ROOT . '/app/core/Controller.php';
require_once APP_ROOT . '/app/core/Session.php';
require_once APP_ROOT . '/app/core/Auth.php';
require_once APP_ROOT . '/app/core/Database.php';

class SfiaController extends Controller
{
    public function index()
    {
        Session::init();
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT * FROM sfia_skills ORDER BY category, code");
        $skills = $stmt->fetchAll();

        $this->view('sfia/index', ['skills' => $skills]);
    }
}
