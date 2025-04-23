<?php
include '../includes/init.php';

// If the user is logged in, fetch their profile picture
if (isset($_SESSION['user_id'])) {

    $user_id = $_SESSION['user_id'];
    
    // Prepare the query to fetch user information including the profile picture
    $query = "SELECT * FROM users WHERE id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
      ':user_id' => $user_id
    ]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // If the user exists and has a profile picture
    $profile_picture = !empty($user['profile_image']) ? $user['profile_image'] : '../assets/profile-default.jpeg';// fallback to a default image if no profile picture
    $email = $user['email'];
    $username = $user['username'];

}
?>


<nav class="navbar navbar-expand-lg">
  <div class="container">
    <!-- Logo + Name -->
    <a class="navbar-brand" href="home.php">
      <img src="../assets/logo.png" alt="NoteNest Logo">
      NoteNest
    </a>

    <!-- Toggler -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
      data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
      aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar links -->
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
      <div class="navbar-nav">
        <a class="nav-link" href="dashboard.php">Dashboard</a>
        <a class="nav-link" href="classrooms.php">Classrooms</a>
        <a class="nav-link" href="notes.php">Notes</a>
        <a class="nav-link" href="contact.php">Contact</a>
      </div>

      <!-- Right side -->
      <div class="auth-links ms-auto">
        <?php if (!isset($_SESSION['user_id'])): ?>
          <a href="login.php"><button class="btn-login">Log in</button></a>
          <a href="signup.php"><button class="btn-signup">Sign up</button></a>
        <?php else: ?>

          <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="../uploads/profile_images/<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" width="36" height="36" class="rounded-circle shadow-sm">
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow" style="min-width: 230px;">
              <li class="px-3 py-2">
                <strong class="d-block"><?php echo htmlspecialchars($username); ?></strong>
                <small class="text-muted"><?php echo htmlspecialchars($email); ?></small>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Profile</a></li>
              <li><a class="dropdown-item" href="settings.php"><i class="bi bi-gear me-2"></i>Settings</a></li>
              <li><a class="dropdown-item text-danger" href="../includes/logout_handler.php"><i class="bi bi-box-arrow-right me-2"></i>Log out</a></li>
            </ul>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>