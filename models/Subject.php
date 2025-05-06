<?php
// app/models/Subject.php
class Subject {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Create a new subject in the database
    public function createSubject($classroom_id, $name, $description) {
        $stmt = $this->pdo->prepare("INSERT INTO classroom_subjects (classroom_id, subject_name, subject_desc) VALUES (?, ?, ?)");
        $stmt->execute([$classroom_id, $name, $description]);
    }

}
