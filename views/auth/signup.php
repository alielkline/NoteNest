<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/assets/css/signup.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">


    <title>NoteNest - Create Account</title>
</head>
    <body>
        <?php if (isset($_SESSION['signup_errors'])): ?>
            <div class="error-message" id = "error-message">
                <?php foreach ($_SESSION['signup_errors'] as $error): ?>
                    <div><?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>
            </div>
            <?php unset($_SESSION['signup_errors']); ?>
        <?php endif; ?>

        <div class="signup-container">
            <div class="logo-container">
                <img src="../../public/assets/images/logo.png" alt="NoteNest Logo">
                <div class="logo-text">NoteNest</div>
            </div>

            <h2>Create an account</h2>
            <p class="subtitle">Enter your information to get started</p>
            
            <form id="signupForm" action="../../controllers/AuthController.php?action=signup" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="John Doe" require>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="•••••••" required>
                </div>
                
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="•••••••" required>
                    <div id="passwordError" class="password-error">Passwords don't match</div>
                </div>
                
                <div class="divider"></div>
                
                <button type="submit" class="btn">Create Account</button>
                
                <p class="login-link">
                    Already have an account? <a href="login.php">Log in</a>
                </p>
            </form>
        </div>

        <script src="../../public/assets/js/signup.js"></script>
        <script src="../../public/assets/js/error.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>

