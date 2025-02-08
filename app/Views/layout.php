<?php
// app/Views/layout.php
// Ensure session is started and constants loaded
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/../../config/constants.php';
?>
<!DOCTYPE html>
<html lang="mk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Invoicing App</title>
  <!-- AdminLTE CSS -->
  <link rel="stylesheet" href="/assets/adminlte/css/adminlte.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/assets/adminlte/plugins/fontawesome-free/css/all.min.css">
  <!-- Optional: your custom CSS -->
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-dark navbar-primary">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="/invoices" class="nav-link">Dashboard</a>
      </li>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <?php if (!isset($_SESSION[SESSION_USER])): ?>
          <li class="nav-item">
            <a class="nav-link" href="/auth/google">Login with Google</a>
          </li>
      <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="/logout">Hello, <?= htmlspecialchars($_SESSION[SESSION_USER]['name'] ?? '') ?> | Logout</a>
          </li>
      <?php endif; ?>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/invoices" class="brand-link">
      <img src="/assets/adminlte/img/AdminLTELogo.png" alt="Logo" class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light">Invoicing</span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          <li class="nav-item">
            <a href="/invoices" class="nav-link">
              <i class="nav-icon fas fa-file-invoice"></i>
              <p>All Invoices</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/invoices/create" class="nav-link">
              <i class="nav-icon fas fa-plus-circle"></i>
              <p>Create Invoice</p>
            </a>
          </li>
          <!-- Add more menu items as needed -->
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <!-- Optionally, add breadcrumbs or page title -->
      </div><!-- /.container-fluid -->
    </section>
    
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <?php
          // Display the content of the specific view
          echo $content ?? '';
        ?>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Footer -->
  <footer class="main-footer">
    <div class="float-right d-none d-sm-inline">
      Invoicing App
    </div>
    <strong>&copy; <?= date('Y') ?> Your Company</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="/assets/adminlte/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 5 -->
<script src="/assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="/assets/adminlte/js/adminlte.min.js"></script>
<!-- Optional: your custom JS -->
<script src="/assets/js/script.js"></script>
</body>
</html>
