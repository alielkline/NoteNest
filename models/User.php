<?php
// app/models/User.php
class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByUsername($username) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($username, $email, $hashedPassword) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO users (username, email, password, profile_image) 
             VALUES (:username, :email, :password, :image)"
        );
        return $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':image' => 'profile-default.jpg'
        ]);
    }

    public function updateGeneral($id, $username, $email) {
        $stmt = $this->pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        return $stmt->execute([$username, $email, $id]);
    }

    public function updatePassword($id, $hashedPassword) {
        $stmt = $this->pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([$hashedPassword, $id]);
    }

    public function updateProfileImage($id, $filename) {
        $stmt = $this->pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
        return $stmt->execute([$filename, $id]);
    }
}
