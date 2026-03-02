<?php

// Run: php scripts/setup.php
// Creates database file and runs all migrations in /database/migrations

define('APP_ROOT', dirname(__DIR__));

$dbDir = APP_ROOT . '/database';
$dbPath = $dbDir . '/bcs-placement-portal.sqlite';
$migrationsDir = $dbDir . '/migrations';

if (!is_dir($dbDir)) {
    mkdir($dbDir, 0755, true);
}

echo "DB path: {$dbPath}\n";

try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("PRAGMA foreign_keys = ON;");
} catch (PDOException $e) {
    fwrite(STDERR, "DB connection failed: " . $e->getMessage() . "\n");
    exit(1);
}

$files = glob($migrationsDir . '/*.sql');
sort($files);

if (!$files) {
    fwrite(STDERR, "No migrations found in {$migrationsDir}\n");
    exit(1);
}

foreach ($files as $file) {
    $sql = file_get_contents($file);
    if ($sql === false) {
        fwrite(STDERR, "Failed to read migration: {$file}\n");
        exit(1);
    }

    echo "Running: " . basename($file) . "\n";
    $pdo->exec($sql);
}

echo "✅ Migrations complete.\n";
echo "You can now run the app and register accounts.\n";