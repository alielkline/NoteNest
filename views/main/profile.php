<?php
include '../../config/init.php';
require_once '../../controllers/ProfileController.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$controller = new ProfileController($_SESSION['user_id']);
$controller->handleRequest(); // will only run on POST

$user = $controller->getUserData();

$username = $user['username'];
$email = $user['email'];
$imagePath = !empty($user['profile_image'])
    ? "../../public/uploads/profile_pictures/" . htmlspecialchars($user['profile_image'])
    : "../../public/uploads/profile_pictures/profile-default.jpg";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO Meta Tags -->
    <meta name="description" content="Manage your account settings and update your profile details.">
    <meta name="keywords" content="profile, settings, account, user, edit, update, secure, dashboard">
    <meta name="author" content="YourProjectName">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/main.css">
    <link rel="stylesheet" href="../../public/assets/css/navbar.css">
    <link rel="stylesheet" href="../../public/assets/css/profile.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <title>Profile</title>
</head>

<body>
    <?php include '../partials/navbar.php'; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert error-message" id="error-message">
            <?php echo htmlspecialchars($_SESSION['error']); ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php elseif (isset($_SESSION['password_changed'])): ?>
        <div class="alert success-message" id="success-message">
            Password changed successfully.
        </div>
        <?php unset($_SESSION['password_success']); ?>
    <?php elseif (isset($_SESSION['success'])): ?>
        <div class="alert success-message" id="success-message">
            <?php echo htmlspecialchars($_SESSION['success']); ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="container py-5">
        <div class="text-center mb-4">
            <h2 class="fw-bold">Profile Settings</h2>
            <p class="text-muted">Manage your account information</p>
        </div>

        <div class="row justify-content-center g-4">
            <!-- Profile Image Section -->
            <div class="col-md-4 text-center">
                <div class="profile-card p-4 bg-white rounded-4 shadow-sm">
                    <div class="position-relative d-inline-block">
                        <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="Profile Picture" class="avatar mb-3" id="profileImage">
                        <form id="photoForm" action="profile.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="form_type" value="photo">
                            <input type="file" id="upload" name="profile_image" accept="image/*" onchange="submitForm()" hidden>
                            <label for="upload" class="upload-icon position-absolute bottom-0 end-0">
                                <img src="../../public/assets/images/camera.png" alt="Upload">
                            </label>
                        </form>
                    </div>
                    <h5 class="fw-bold"><?php echo htmlspecialchars($username); ?></h5>
                    <p class="text-muted"><?php echo htmlspecialchars($email); ?></p>
                </div>
            </div>

            <!-- Tabs + Forms Section -->
            <div class="col-md-8">
                <div class="profile-card bg-white p-4 rounded-4 shadow-sm">
                    <div class="tabs d-flex mb-4 border-bottom">
                        <div class="tab active" onclick="showTab('general')">General</div>
                        <div class="tab" onclick="showTab('security')">Security</div>
                    </div>

                    <!-- General Information -->
                    <form id="general" class="tab-content" action="profile.php" method="POST">
                        <input type="hidden" name="form_type" value="general">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-purple w-100">Save Changes</button>
                    </form>

                    <!-- Security Tab -->
                    <form id="security" class="tab-content d-none" action="profile.php" method="POST">
                        <input type="hidden" name="form_type" value="security">
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-purple w-100">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
<?php include '../partials/footer.php'; ?>