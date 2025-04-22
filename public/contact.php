<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/contact.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">




    <title>Contact</title>
</head>
<body>

    <!--Include navbar !-->
    <?php include '../includes/navbar.php'; ?>
    
        <!-- Check session incase the user left a field empty !-->
        <?php if (isset($_SESSION['contact_errors'])): ?>
            <div class="error-message">
                <?php foreach ($_SESSION['contact_errors'] as $error): ?>
                    <div><?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>
            </div>
            <?php unset($_SESSION['contact_errors']); ?>
        <?php endif; ?>

        

    <div class="container">
        <div class="row container-fluid">
            <div class="col-8 mx-auto">
                <h2 class="header py-4 w-100">Contact Us</h2>

                    <!-- Main Form !-->
                    <form action="../includes/contact_handler.php" method="post">

                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-person align-text-bottom" viewBox="0 0 16 16">
                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                        </svg>
                        
                        <label for="contactName" class="align-text-top mx-1" ><b>Name</b></label><br>
                        <input class="form-control my-3" id="contactName" type="text" name="name" placeholder="Your Name" >

                        
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope align-text-bottom" viewBox="0 0 16 16">
                        <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z"/>
                        </svg>

                        <label for="contactEmail" class="align-text-top mx-1"><b>Email</b></label><br>
                        <input class="form-control my-3" id="contactEmail" type="text" name="email" placeholder="example@email.com">

                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat-left align-text-bottom" viewBox="0 0 16 16">
                        <path d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4.414A2 2 0 0 0 3 11.586l-2 2V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                        </svg>

                        <label for="contactMessage" class="align-text-top mx-1"><b>Message</b></label><br>
                        <textarea class="form-control my-3" id="contactMessage" rows="10" name="message" placeholder="Write your feedback here!"></textarea>

                        <button class="btn-signup w-100 my-2" type="submit" name="submit">Send Message</button>

                    </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>