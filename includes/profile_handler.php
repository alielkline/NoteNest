<?php
include '../includes/init.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!isset($_POST['form_type'])) {
        header("Location: ../public/profile.php?error=form_type_missing");
        exit();
    }

    $form_type = $_POST['form_type'];

    if($form_type === 'photo'){
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['profile_image']['tmp_name'];
            $fileName = $_FILES['profile_image']['name'];
            $fileSize = $_FILES['profile_image']['size'];
            $fileType = $_FILES['profile_image']['type'];

            // File extension validation
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($fileExtension, $allowedfileExtensions)) {
                $newFileName = $user_id . '_profile.' . $fileExtension;
                $uploadFileDir = '../uploads/profile_images/';
                $dest_path = $uploadFileDir . $newFileName;

                // Create directory if not exists
                if (!file_exists($uploadFileDir)) {
                    mkdir($uploadFileDir, 0777, true);
                }

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    // Update the image path in the database
                    $stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
                    $stmt->execute([$newFileName, $user_id]);
                } else {
                    header("Location: ../public/profile.php?error=file_upload_failed");
                    exit();
                }
            } else {
                header("Location: ../public/profile.php?error=invalid_file_extension");
                exit();
            }
        } else {
            header("Location: ../public/profile.php?error=file_upload_error");
            exit();
        }
    }

    // Updating general info (username & email)
    if ($form_type === "general") {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);

        if (!empty($username) && !empty($email)) {
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $stmt->execute([$username, $email, $user_id]);

            header("Location: ../public/profile.php");
            exit();
        } else {
            header("Location: ../public/profile.php");
            exit();
        }
    }

    // Updating password
    elseif ($form_type === "security") {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (!empty($new_password) && !empty($confirm_password)) {
            if ($new_password === $confirm_password) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $user_id]);

                header("Location: ../public/profile.php?password_changed=1");
                exit();
            } else {
                header("Location: ../public/profile.php?error=password_mismatch");
                exit();
            }
        } else {
            header("Location: ../public/profile.php?error=empty_password");
            exit();
        }
    }

    // Unknown form type
    else {
        header("Location: ../public/profile.php?error=invalid_form_type");
        exit();
    }

} else {
    header("Location: ../public/profile.php");
    exit();
}
?>
