<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/navbar.css">

    <title>Dashboard</title>
</head>

<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="main-content p-4 w-100">
        <h2>Welcome back, <?php echo $_SESSION["username"] ?></h2>
        <p class="text-muted">Here’s an overview of your Notenest activities</p>

        <!-- Tabs -->
        <ul class="nav nav-tabs mt-4" id="dashboardTabs">
            <li class="nav-item">
                <a class="nav-link active" href="#">Overview</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Classrooms</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Bookmarks</a>
            </li>
        </ul>

        <!-- Cards -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card p-3">
                    <h5>My Classrooms</h5>
                    <p>0</p>
                    <small>Classrooms you're enrolled in</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-3">
                    <h5>Bookmarked Notes</h5>
                    <p>0</p>
                    <small>Notes you’ve saved for later</small>
                </div>
            </div>
            <div class="col-md-6 mt-3">
                <div class="card p-3">
                    <h5>Recent Classrooms</h5>
                    <small>No classrooms yet</small>
                </div>
            </div>
            <div class="col-md-6 mt-3">
                <div class="card p-3">
                    <h5>Bookmarked Notes</h5>
                    <small>No bookmarked notes yet</small>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>