// migrations/20240520_create_users_table.php
<?php
function up(PDO $pdo) {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255),
            email VARCHAR(255) UNIQUE,
            password VARCHAR(255),
            profile_image VARCHAR(255) DEFAULT 'profile-default.jpg',
            remember_token VARCHAR(255),
            remember_token_expiry DATETIME,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
}

function down(PDO $pdo) {
    $pdo->exec("DROP TABLE IF EXISTS users");
}
