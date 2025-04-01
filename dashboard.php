<?php
// Start the session
// Запустить сессию
session_start();

// Check if user is logged in
// Проверить, авторизован ли пользователь
if (!isset($_SESSION["employee_id"])) {
    // Redirect to login if not logged in
    // Перенаправить на страницу входа, если не авторизован
    header("Location: login.php");
    exit;
}

// Load user data from session
// Загрузить данные пользователя из сессии
$name = $_SESSION["name"];
$employee_id = $_SESSION["employee_id"];
$is_admin = $_SESSION["is_admin"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard | NKEY System</title>
  <link rel="icon" href="/img/nkey.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/css/style.css?v=1.0.0">
</head>
<body>

  <!-- Header -->
  <!-- Заголовок -->
  <header class="d-flex justify-content-between align-items-center p-3 bg-dark text-white">
    <div class="d-flex align-items-center">
      <img src="/img/logo.jpg" alt="NKEY Logo" width="40" height="40" class="me-2">
      <h4 class="mb-0">NKEY System</h4>
    </div>
    <div class="d-flex align-items-center">
      <span class="me-2"><?= htmlspecialchars($name) ?> (ID: <?= $employee_id ?>)</span>
      <img src="/uploads/<?= $employee_id ?>.jpg" alt="Profile" width="40" height="40" class="rounded-circle">
    </div>
  </header>

  <div class="d-flex">

    <!-- Sidebar -->
    <!-- Боковая панель -->
    <nav class="bg-light border-end p-3" style="width: 200px; min-height: 100vh;">
      <ul class="nav flex-column">
        <li class="nav-item mb-2"><a href="dashboard.php" class="nav-link text-dark">🏠 Home</a></li>
        <li class="nav-item mb-2"><a href="my_page.php" class="nav-link text-dark">👤 My Page</a></li>
        <li class="nav-item mb-2"><a href="my_timesheet.php" class="nav-link text-dark">📊 My Timesheet</a></li>
        <li class="nav-item mb-2"><a href="vacation.php" class="nav-link text-dark">🌴 My Vacation</a></li>
        <li class="nav-item mb-2"><a href="logout.php" class="nav-link text-dark">🚪 Logout</a></li>

        <?php if ($is_admin): ?>
        <!-- Admin menu -->
        <!-- Меню администратора -->
        <hr>
        <li class="nav-item mb-2"><a href="users.php" class="nav-link text-dark">👥 USERS</a></li>
        <li class="nav-item mb-2"><a href="admin_timesheet.php" class="nav-link text-dark">📅 Timesheet</a></li>
        <li class="nav-item"><a href="admin_vacation.php" class="nav-link text-dark">📝 Vacation</a></li>
        <?php endif; ?>
      </ul>
    </nav>

    <!-- Main content -->
    <!-- Основное содержимое -->
    <main class="flex-grow-1 p-5">
      <div class="p-4 bg-white rounded shadow">
        <h2>Welcome, <?= htmlspecialchars($name) ?>!</h2>
        <p>This is your employee dashboard.</p>
        <?php if ($is_admin): ?>
          <p><strong>You are logged in as an Administrator.</strong></p>
        <?php else: ?>
          <p><strong>You are logged in as a regular User.</strong></p>
        <?php endif; ?>
      </div>
    </main>
  </div>

</body>
</html>
