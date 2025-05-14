<?php
require_once __DIR__ .'/../../config/init.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/assets/css/signup.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <title>NoteNest - Log in</title>
</head>
<body>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-message" id="error-message"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['signup_success'])): ?>
        <div class="success-message" id="success-message">
            <?php echo htmlspecialchars($_SESSION['signup_success']); unset($_SESSION['signup_success']); ?>
        </div>
    <?php endif; ?>

    <div class="signup-container">
        <div class="logo-container">
            <img src="../../public/assets/images/logo.png" alt="NoteNest Logo">
            <div class="logo-text">NoteNest</div>
        </div>

        <h2>Log In</h2>
        
        <form id="signupForm" action="../../controllers/AuthController.php?action=login" method="POST"> <!-- Handle login action here -->
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="•••••••" required>
            </div>
            
            <div class="divider"></div>
            
            <button type="submit" class="btn btn-primary">Log in</button>
            
            <p class="login-link">
                Don't have an account? <a href="signup.php">Sign Up</a>
            </p>
        </form>
    </div>

        <script src="../../public/assets/js/signup.js"></script>
        <script src="../../public/assets/js/error.js"></script>
        <script src="../../public/assets/js/success.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
