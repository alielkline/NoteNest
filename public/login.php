<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/signup.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <title>NoteNest - Log in</title>
</head>
    <body>
        <?php 
        session_start();
        if (isset($_SESSION['error'])): ?>
            <div class="error-message" id = "error-message"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['signup_success'])): ?>
            <div class="success-message" id = success-message>
                <?php echo htmlspecialchars($_SESSION['signup_success']); ?>
            </div>
            <?php unset($_SESSION['signup_success']); ?>
        <?php endif; ?>


        <div class="signup-container">
            <div class="logo-container">
                <img src="../assets/logo.png" alt="NoteNest Logo">
                <div class="logo-text">NoteNest</div>
            </div>

            <h2>Log In</h2>
            
            <form id="signupForm" action="../includes/login_handler.php" method="POST">
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="•••••••" required>
                </div>
                
                <div class="divider"></div>
                
                <button type="submit" class="btn">Log in</button>
                
                <p class="login-link">
                    Dont have an account? <a href="signup.php">Sign Up</a>
                </p>
            </form>
        </div>

        <script src="../js/signup.js"></script>
        <script src="../js/error.js"></script>
        <script src="../js/success.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>

