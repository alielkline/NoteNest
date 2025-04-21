<?php
$user_name = $_SESSION["username"];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/navbar.css">

    <title>Home</title>
</head>

<body>
    <?php include '../includes/navbar.php'; ?>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="../includes/logout_handler.php" class="btn btn-danger mx-2">Logout</a>
    <?php endif; ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>