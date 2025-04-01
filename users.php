<?php
// Enable error reporting
// Включить отображение ошибок
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start session
// Запустить сессию
session_start();

// Check if user is logged in and is admin
// Проверить, авторизован ли пользователь и является ли администратором
if (!isset($_SESSION["employee_id"]) || $_SESSION["is_admin"] != 1) {
    header("Location: login.php");
    exit;
}

require_once "includes/config.php";

// Fetch all users
// Получить всех пользователей
$stmt = $conn->query("SELECT employee_id, name, email, department, is_admin FROM users ORDER BY employee_id ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Users | Admin Panel</title>
  <link rel="icon" href="/img/nkey.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/css/style.css?v=1.0.0">
</head>
<body>

<div class="container py-5">
  <div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h4 class="mb-0">👥 All Users</h4>
      <div>
        <a href="dashboard.php" class="btn btn-secondary me-2">⬅ Back to Dashboard</a>
        <a href="add_user.php" class="btn btn-success">➕ New User</a>
      </div>
    </div>

    <div class="card-body">
      <table class="table table-bordered table-hover">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Department</th>
            <th>Admin</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $user): ?>
            <tr>
              <td><?= $user['employee_id'] ?></td>
              <td><?= htmlspecialchars($user['name']) ?></td>
              <td><?= htmlspecialchars($user['email']) ?></td>
              <td><?= htmlspecialchars($user['department']) ?></td>
              <td><?= $user['is_admin'] ? 'Yes' : 'No' ?></td>
              <td>
                <a href="edit_user.php?id=<?= $user['employee_id'] ?>" class="btn btn-sm btn-outline-primary">View/Edit</a>
                <a href="delete_user.php?id=<?= $user['employee_id'] ?>" class="btn btn-sm btn-outline-danger ms-2" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

</body>
</html>
