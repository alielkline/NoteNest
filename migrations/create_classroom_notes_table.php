<?php
function up(PDO $pdo) {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS classroom_notes (
            note_id INT AUTO_INCREMENT PRIMARY KEY,
            uploader_user_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            content TEXT,
            visibility ENUM('public', 'private') DEFAULT 'private',
            attachment VARCHAR(255),
            subject_id INT NOT NULL,
            likes INT DEFAULT 0,
            upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (uploader_user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (subject_id) REFERENCES classroom_subjects(subject_id) ON DELETE CASCADE
        )
    ");
}

function down(PDO $pdo) {
    $pdo->exec("DROP TABLE IF EXISTS classroom_notes");
}
