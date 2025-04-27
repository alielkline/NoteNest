<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/navbar.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <title>Home</title>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <?php if (isset($_GET['logout']) && $_GET['logout'] == 'success'): ?>
    <div class="alert alert-success">You have been logged out successfully.</div>
    <?php endif; ?>

    
    <div class="container" style="text-align: center;">
        <div class="row mt-5 justify-content-center">
            <div class="col-auto">
                <div class="d-inline-flex align-items-baseline">
                    <img src="/NoteNest/assets/logo.png" alt="NoteNest_Logo"
                        style="height: 40px; margin-right: 10px; border-radius: 20%">
                            <h1 class="display-5 mb-0" style="font-family: 'Inter'; font-weight: 600; color:#6E59A5;">
                NoteNest
            </h1>
            </div>
        </div>
        </div>
        <div class="row">
            <div class="col">           
                    <h1 class="display-1" style="font-family: 'inter'; font-weight: 400; color: #020817">
                    <b>Collaborative note-taking for better learning</b>
                    </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-6 mx-auto text-muted">
                <p style="font-size: 25px">Create classrooms, share notes, and learn together with your classmates in a collaborative environment.</p>
            </div>
        </div>
        <div class="row"></div>
        <div class="col mx-auto">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="signup.php" class="btn-signup p-3 px-5 mx-2" style="text-decoration: none;">Get Started For Free</a>
                <a href="login.php" class="btn-login p-3 px-5 mx-2" style="border: 1px solid lightgrey; border-radius: 10px; text-decoration: none;">Log in</a>
            <?php endif; ?>
        </div>
        <div class="row" style="margin-top:10%">
            <div class="col-4">
                <div class="card shadow p-3 mb-5 bg-white rounded" style="width: 100%;">
                    <div class="card-body">
                
                    <div class="home-icons">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-book" viewBox="0 0 16 16">
                        <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/>
                        </svg>
                    </div>

                        <h5 style="font-weight: 900">Create Classrooms</h5>
                        <p class="card-text">Organize your study groups by creating interactive classrooms for any subject or course.</p>                
                    </div>
                </div>
            </div>
            <div class="col-4">
            <div class="card shadow p-3 mb-5 bg-white rounded" style="width: 100%;">
                    <div class="card-body">
                        <h5 style="font-weight: 900">Take Rich Notes</h5>
                        <p class="card-text">Create comprehensive notes with text, images, videos, and attachments to enhance learning.</p>  
                    </div>
                </div>
            </div>
                
            <div class="col-4">
            <div class="card shadow p-3 mb-5 bg-white rounded" style="width: 100%;">
                    <div class="card-body">
                        <h5 style="font-weight: 900">Collaborate</h5>
                        <p class="card-text">Share notes, give feedback, and bookmark useful content from your classmates.</p>
                        
                    </div>
                </div>
            </div>
        </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>