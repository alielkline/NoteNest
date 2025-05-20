<?php
function up(PDO $pdo) {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS likes (
            like_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            note_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_like (user_id, note_id),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (note_id) REFERENCES classroom_notes(note_id) ON DELETE CASCADE
        )
    ");
}

function down(PDO $pdo) {
    $pdo->exec("DROP TABLE IF EXISTS likes");
}
