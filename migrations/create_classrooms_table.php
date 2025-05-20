<?php
function up(PDO $pdo) {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS classrooms (
            classroom_id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            visibility ENUM('public', 'private') DEFAULT 'private',
            invite_code VARCHAR(255) UNIQUE,
            creator_id INT NOT NULL,
            members INT DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
}

function down(PDO $pdo) {
    $pdo->exec("DROP TABLE IF EXISTS classrooms");
}
