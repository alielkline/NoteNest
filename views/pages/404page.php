<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>404 Page Not Found</title>
  <link  href="../../public/assets/css/404page.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center min-vh-100">

  <div class="text-center">
    <h1 class="display-1 fw-bold">404</h1>
    <h2 class="mb-3">OOPS! PAGE NOT FOUND</h2>
    <p class="text-muted mb-4">
      Sorry, the page you're looking for doesn't exist. If you think something is broken, report a problem.
    </p>
    <div class="d-flex justify-content-center gap-3 flex-wrap">
      <a href="../main/home.php" class="btn-custom">RETURN HOME</a>
      <button class="btn-custom" onclick="reportProblem()">REPORT PROBLEM</button>
    </div>
  </div>

<?php include '../partials/footer.php'; ?>
