<?php

class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        $path = APP_ROOT . "/database/bcs-placement-portal.sqlite";

        try {
            $this->pdo = new PDO("sqlite:" . $path);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->exec("PRAGMA foreign_keys = ON");
        } catch (PDOException $e) {
            die("DB Connection Failed: " . htmlspecialchars($e->getMessage()));
        }
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->pdo;
    }
}
