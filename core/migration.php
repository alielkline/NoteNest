<?php
// migrate.php

require_once __DIR__ . '/../config/init.php';
$pdo = Database::getConnection();

$migrationsDir = __DIR__ . '/migrations';
$migrationFiles = scandir($migrationsDir);
$logFile = __DIR__ . '/migrations.log';

if (!file_exists($logFile)) {
    file_put_contents($logFile, json_encode([]));
}

$executed = json_decode(file_get_contents($logFile), true);

foreach ($migrationFiles as $file) {
    if ($file === '.' || $file === '..') continue;
    if (!in_array($file, $executed)) {
        echo "Running migration: $file\n";
        require "$migrationsDir/$file";
        if (function_exists('up')) {
            up($pdo);
        } else {
            echo "Migration $file missing up() function.\n";
            continue;
        }
        $executed[] = $file;
    }
}

file_put_contents($logFile, json_encode($executed));
echo "✅ Migrations complete.\n";
