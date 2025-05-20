<?php
function up(PDO $pdo) {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS comments (
            comment_id INT AUTO_INCREMENT PRIMARY KEY,
            note_id INT NOT NULL,
            user_id INT NOT NULL,
            comment_text TEXT NOT NULL,
            comment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (note_id) REFERENCES classroom_notes(note_id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
}

function down(PDO $pdo) {
    $pdo->exec("DROP TABLE IF EXISTS comments");
}
