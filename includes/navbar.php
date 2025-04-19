<?php
session_start();
require_once 'db.inc.php'; // Include the database connection

// If the user is logged in, fetch their profile picture
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Prepare the query to fetch user information including the profile picture
    $query = "SELECT profile_picture FROM users WHERE id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
      ':user_id' => $user_id
    ]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // If the user exists and has a profile picture
    $profile_picture = $user['profile_picture'] ?? '../uploads/default-image.jpg'; // fallback to a default image if no profile picture
}
?>

<nav class="navbar navbar-expand-md">
  <div class="container-fluid">
    <!-- Logo + Name-->
    <a class="navbar-brand" href="home.php"> 
      <img src="../assets/logo.png" alt="NoteNest Logo">
      NoteNest
    </a>

    <!-- Toggler button -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
      data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
      aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- The dropdown tabs -->
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
      <div class="navbar-nav">
        <a class="nav-link" href="dashboard.php">Dashboard</a>
        <a class="nav-link" href="classrooms.php">Classrooms</a>
        <a class="nav-link" href="notes.php">Notes</a>
        <a class="nav-link" href="contact.php">Contact</a>
      </div>

      <!-- Log in and sign up buttons, only shown if the user is not logged in -->
      <div class="auth-links">
        <?php if (!isset($_SESSION['user_id'])): ?>
          <a href="login.php"><button class="btn-login">Log in</button></a>
          <a href="signup.php"><button class="btn-signup">Sign up</button></a>
        <?php else: ?>
          <a href="profile.php" class="profile-image">
          <img src="../uploads/<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture"/>
          </a>
        <?php endif; ?>
      </div>
    </div>

  </div>
</nav>
