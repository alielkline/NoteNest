<?php
require_once __DIR__ .'/../../config/init.php';

$email = isset($_COOKIE['remember_email']) ? htmlspecialchars($_COOKIE['remember_email']) : '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO Meta Tags -->
    <meta name="title" content="Log in - NoteNest">
    <meta name="description" content="Access your NoteNest account to collaborate on notes, join classrooms, and enhance your learning experience.">
    <meta name="keywords" content="NoteNest, login, student collaboration, notes, study groups">
    <meta name="author" content="NoteNest Team">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/main.css">
    <link rel="stylesheet" href="../../public/assets/css/signup.css">

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
                <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required value="<?php echo $email; ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="•••••••" required>
            </div>
            
            <div class="divider"></div>
            
            <button type="submit" class="btn btn-primary">Log in</button>
            
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label mt-3" for="remember">Remember Me</label>
            </div>

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
