<?php
require_once __DIR__ .'/../../config/init.php';
require_once __DIR__ .'/../../controllers/ProfileController.php';

if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
  $controller = new ProfileController($user_id);
  $user = $controller->getUserData();

  $profile_picture = '../../public/uploads/profile_pictures/' . $user['profile_image'];
  $email = $user['email'];
  $username = $user['username'];
}

?>


<nav class="navbar navbar-expand-lg">
  <div class="container">
    <!-- Logo + Name -->
    <a class="navbar-brand" href="../main/home.php">
      <img src="../../public/assets/images/logo.png" alt="NoteNest Logo">
      NoteNest
    </a>

    <!-- Toggler -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
      data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
      aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <?php
    $currentPage = basename($_SERVER['PHP_SELF']);
    ?>

    <!-- Navbar links -->
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
      <div class="navbar-nav">
        <a class="nav-link <?= ($currentPage === 'dashboard.php') ? 'active text-primary' : '' ?>" href="../main/dashboard.php">Dashboard</a>
        <a class="nav-link <?= ($currentPage === 'classrooms.php') ? 'active text-primary' : '' ?>" href="../pages/classrooms.php">Classrooms</a>
        <a class="nav-link <?= ($currentPage === 'notes.php') ? 'active text-primary' : '' ?>" href="../notes/notes.php">Notes</a>
        <a class="nav-link <?= ($currentPage === 'contact.php') ? 'active text-primary' : '' ?>" href="../pages/contact.php">Contact</a>
      </div>

      <!-- Right side -->
      <div class="auth-links ms-auto">
        <?php if (!isset($_SESSION['user_id'])): ?>
          <a href="../auth/login.php"><button class="btn-login">Log in</button></a>
          <a href="../auth/signup.php"><button class="btn-signup">Sign up</button></a>
        <?php else: ?>

          <div class="dropdown position-relative">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" width="36" height="36" class="rounded-circle shadow-sm">
            </a>
            <ul class="dropdown-menu shadow custom-dropdown">
              <li class="px-3 py-2">
                <strong class="d-block"><?php echo htmlspecialchars($username); ?></strong>
                <small class="text-muted"><?php echo htmlspecialchars($email); ?></small>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li><a class="dropdown-item" href="../main/profile.php"><i class="bi bi-person me-2"></i>Profile</a></li>
              <li><a class="dropdown-item text-danger" href="../../controllers/AuthController.php?action=logout"><i class="bi bi-box-arrow-right me-2"></i>Log out</a></li>
            </ul>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>