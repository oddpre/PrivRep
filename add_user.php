<?php
// add_user.php - Add new user form (admin only)
// add_user.php - Форма добавления нового пользователя (только для администратора)

session_start();

// Check admin access
// Проверить доступ администратора
if (!isset($_SESSION['employee_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add New User | NKEY System</title>
  <link rel="icon" href="/img/nkey.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
  <div class="card mx-auto shadow" style="max-width: 600px;">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h4>Add New User</h4>
      <a href="users.php" class="btn btn-secondary">⬅ Back</a>
    </div>
    <div class="card-body">
      <form action="insert_user.php" method="post">
        <div class="mb-3">
          <label class="form-label">Name</label>
          <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Phone</label>
          <input type="text" name="phone" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">RFID ID</label>
          <input type="text" name="rfid_id" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Department</label>
          <input type="text" name="department" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Manager Email</label>
          <input type="email" name="manager_email" class="form-control" value="manager@nkey.no">
        </div>
        <div class="mb-3">
          <label class="form-label">Admin</label>
          <select name="is_admin" class="form-select">
            <option value="0">No</option>
            <option value="1">Yes</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="text" name="password" class="form-control" value="password" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Confirm Password</label>
          <input type="text" name="confirm" class="form-control" value="password" required>
        </div>
        <button type="submit" class="btn btn-primary">➕ Add User</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>