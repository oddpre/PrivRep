<?php
// Enable error reporting
// Включить отображение ошибок
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start session and protect page
// Запуск сессии и защита страницы
session_start();
if (!isset($_SESSION["employee_id"])) {
    header("Location: login.php");
    exit();
}

require_once "includes/config.php";

// Fetch current user data from DB
// Получить данные текущего пользователя из БД
$employee_id = $_SESSION["employee_id"];
$stmt = $conn->prepare("SELECT * FROM users WHERE employee_id = ?");
$stmt->execute([$employee_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Profile | NKEY System</title>
  <link rel="icon" href="/img/nkey.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/css/style.css?v=1.0.0">
</head>
<body>

<div class="container py-5">
  <div class="card mx-auto shadow-lg" style="max-width: 500px;">
    <div class="card-body text-center">
      <form action="upload_image.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="employee_id" value="<?= $employee_id ?>">
        <img src="<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile" class="rounded-circle mb-3" width="120" height="120">
        <div class="mb-3">
          <input type="file" name="profile_image" class="form-control">
          <button type="submit" class="btn btn-sm btn-outline-primary mt-2">Upload New Image</button>
        </div>
      </form>

      <h4 class="mb-3">Hello, <?= htmlspecialchars($user['name']) ?></h4>

      <ul class="list-group text-start">
        <li class="list-group-item">
          <strong>ID:</strong> <?= $user['employee_id'] ?>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <span><strong>Name:</strong> <span id="nameDisplay"><?= htmlspecialchars($user['name']) ?></span></span>
          <button class="btn btn-sm btn-outline-primary" onclick="editField('name')">Edit</button>
        </li>
        <li class="list-group-item d-none" id="nameEditRow">
          <form onsubmit="return saveField(event, 'name')">
            <div class="input-group">
              <input type="text" name="name" id="nameInput" class="form-control" value="<?= htmlspecialchars($user['name']) ?>">
              <button class="btn btn-success" type="submit">Save</button>
              <button class="btn btn-secondary" type="button" onclick="cancelEdit('name')">Cancel</button>
            </div>
          </form>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <span><strong>Phone:</strong> <span id="phoneDisplay"><?= htmlspecialchars($user['phone']) ?></span></span>
          <button class="btn btn-sm btn-outline-primary" onclick="editField('phone')">Edit</button>
        </li>
        <li class="list-group-item d-none" id="phoneEditRow">
          <form onsubmit="return saveField(event, 'phone')">
            <div class="input-group">
              <input type="text" name="phone" id="phoneInput" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>">
              <button class="btn btn-success" type="submit">Save</button>
              <button class="btn btn-secondary" type="button" onclick="cancelEdit('phone')">Cancel</button>
            </div>
          </form>
        </li>
        <li class="list-group-item">
          <strong>Department:</strong> <?= htmlspecialchars($user['department']) ?>
        </li>
        <li class="list-group-item">
          <strong>Manager Email:</strong> <?= htmlspecialchars($user['manager_email']) ?>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <span><strong>RFID:</strong> <span id="rfid_idDisplay"><?= htmlspecialchars($user['rfid_id'] ?? '') ?></span></span>
          <button class="btn btn-sm btn-outline-primary" onclick="editField('rfid_id')">Edit</button>
        </li>
        <li class="list-group-item d-none" id="rfid_idEditRow">
          <form onsubmit="return saveField(event, 'rfid_id')">
            <div class="input-group">
              <input type="text" name="rfid_id" id="rfid_idInput" class="form-control" value="<?= htmlspecialchars($user['rfid_id'] ?? '') ?>">
              <button class="btn btn-success" type="submit">Save</button>
              <button class="btn btn-secondary" type="button" onclick="cancelEdit('rfid_id')">Cancel</button>
            </div>
          </form>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <span><strong>Password:</strong> ********</span>
          <button class="btn btn-sm btn-outline-primary" onclick="editField('password')">Edit</button>
        </li>
        <li class="list-group-item d-none" id="passwordEditRow">
          <form onsubmit="return saveField(event, 'password')">
            <div class="input-group">
              <input type="password" name="password" id="passwordInput" class="form-control" placeholder="New Password">
              <input type="password" name="confirm" id="confirmInput" class="form-control" placeholder="Confirm Password">
              <button class="btn btn-success" type="submit">Save</button>
              <button class="btn btn-secondary" type="button" onclick="cancelEdit('password')">Cancel</button>
            </div>
          </form>
        </li>
        <li class="list-group-item">
          <strong>Last Login:</strong> <?= htmlspecialchars($user['last_login']) ?>
        </li>
      </ul>
      <a href="dashboard.php" class="btn btn-secondary mt-3">⬅ Back to Dashboard</a>
    </div>
  </div>
</div>

<script>
function editField(field) {
  document.getElementById(field + "Display")?.parentElement.parentElement.classList.add("d-none");
  document.getElementById(field + "EditRow")?.classList.remove("d-none");
}

function cancelEdit(field) {
  document.getElementById(field + "EditRow")?.classList.add("d-none");
  document.getElementById(field + "Display")?.parentElement.parentElement.classList.remove("d-none");
}

function saveField(event, field) {
  event.preventDefault();
  let value;
  if (field === "password") {
    const pass = document.getElementById("passwordInput").value;
    const confirm = document.getElementById("confirmInput").value;
    if (pass !== confirm || pass.length < 4) {
      alert("Passwords do not match or are too short.");
      return;
    }
    value = pass;
  } else {
    value = document.getElementById(field + "Input").value;
  }

  fetch("update_profile.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body: `field=${field}&value=${encodeURIComponent(value)}`
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      if (field !== "password") {
        document.getElementById(field + "Display").innerText = value;
      }
      cancelEdit(field);
    } else {
      alert("Error updating field: " + data.error);
    }
  })
  .catch(error => alert("Request failed: " + error));
}
</script>

</body>
</html>
