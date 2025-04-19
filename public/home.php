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
            <div class="col-6 mx-auto" style="color: #64748B">
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