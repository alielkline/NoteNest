<?php
// rollback.php

require_once __DIR__ . '/../config/init.php';
$pdo = Database::getConnection();

$logFile = __DIR__ . '/migrations.log';
$migrations = json_decode(file_get_contents($logFile), true);

if (empty($migrations)) {
    echo "Nothing to rollback.\n";
    exit;
}

$lastMigration = array_pop($migrations);
require_once "migrations/$lastMigration";

if (function_exists('down')) {
    echo "Rolling back: $lastMigration\n";
    down($pdo);
    file_put_contents($logFile, json_encode($migrations));
    echo "✅ Rolled back.\n";
} else {
    echo "Migration $lastMigration missing down() function.\n";
}