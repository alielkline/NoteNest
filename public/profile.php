<?php
include '../includes/init.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$stmt = $pdo->prepare("SELECT username, email, profile_image FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

$username = $user['username'];
$email = $user['email'];
$imagePath = !empty($user['profile_image']) 
    ? "../uploads/profile_images/" . htmlspecialchars($user['profile_image'])
    : "../assets/profile-default.jpeg";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/profile.css">
    <title>Profile</title>
</head>

<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="header">
        <h3>Profile Settings</h3>
        <p>Manage your account information</p>
    </div>

    <div class="picture-container">
        <div class="avatar-container">
            <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="Profile Picture" class="avatar" id="profileImage">
            <form id="photoForm" action="../includes/profile_handler.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="form_type" value="photo">
                <input type="file" id="upload" name="profile_image" accept="image/*" style="display:none;">
                <label for="upload" class="upload-icon">
                    <img src="../assets/camera.png" alt="Upload">
                </label>
            </form>
        </div>

        <h3><?php echo htmlspecialchars($username);?></h3>
        <p><?php echo htmlspecialchars($email);; ?></p>
    </div>

    <div class="tabs">
        <div class="tab active" onclick="showTab('general')">General</div>
        <div class="tab" onclick="showTab('security')">Security</div>
    </div>

    <!-- General Information Form -->
    <form id="general" class="tab-content" action="../includes/profile_handler.php" method="POST">
        <h4>General Information</h4>
        <p>Update your personal details</p>

        <!-- Form Type Identifier -->
        <input type="hidden" name="form_type" value="general">

        <label>Username</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" class="form-control mb-2" required>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" class="form-control mb-3" required>

        <button type="submit" class="btn save-btn">Save Changes</button>
    </form>

    <!-- Password Change Form -->
    <form id="security" class="tab-content" action="../includes/profile_handler.php" method="POST">
        <h4>Security Settings</h4>
        <p>Manage your password and authentication</p>

        <!-- Form Type Identifier -->
        <input type="hidden" name="form_type" value="security">

        <label>New Password</label>
        <input type="password" name="new_password" class="form-control mb-2" required>

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control mb-3" required>

        <button type="submit" class="btn save-btn">Change Password</button>
    </form>

    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="../includes/logout_handler.php" class="btn btn-danger mx-2">Logout</a>
    <?php endif; ?>

    <script src="../js/profile.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

