<?php
function up(PDO $pdo) {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS classroom_subjects (
            id INT AUTO_INCREMENT PRIMARY KEY,
            classroom_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
}

function down(PDO $pdo) {
    $pdo->exec("DROP TABLE IF EXISTS classroom_subjects");
}
