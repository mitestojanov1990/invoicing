<!DOCTYPE html>
<html lang="mk">
<head>
  <meta charset="UTF-8">
  <!-- Ensures proper rendering on mobile devices -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Invoicing App</title>

  <!-- Bootstrap 5 CSS (CDN) -->
  <link
    rel="stylesheet"
    href="/assets/css/bootstrap.min.css"
  >

  <!-- Optional: your custom CSS after Bootstrap to override styles -->
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="/invoices">My Invoicing</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
            aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="/invoices">All Invoices</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/invoices/create">Create Invoice</a>
        </li>
        <li class="nav-item">
          <?php if (empty($_SESSION[SESSION_USER])): ?>
              <a class="nav-link" href="/auth/google">Login with Google</a>
          <?php else: ?>
              
              <a class="nav-link" href="/logout">Hello, <?= htmlspecialchars($_SESSION[SESSION_USER]['name'] ?? '') ?> | Logout</a>
          <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container my-4">
  <!-- Here we display the main content from each view -->
  <?php echo $content ?? ''; ?>
</div>

<!-- Bootstrap 5 JS (CDN) -->
<script src="/assets/js/bootstrap.bundle.min.js">
</script>

<!-- jQuery if you need it -->
<script src="/assets/js/jquery-3.6.0.min.js"></script>


</body>
</html>
