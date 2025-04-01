<?php
// Enable error reporting
// Включить отображение ошибок
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start session
// Запустить сессию
session_start();

// Check admin access
// Проверить, является ли пользователь администратором
if (!isset($_SESSION["employee_id"]) || $_SESSION["is_admin"] != 1) {
    header("Location: login.php");
    exit;
}

// Include DB connection
// Подключить базу данных
require_once "includes/config.php";

// Get user ID from URL
// Получить ID пользователя из URL
$user_id = $_GET['id'] ?? null;
if (!$user_id) {
    echo "User ID is missing.";
    exit;
}

// Fetch user data
// Получить данные пользователя
$stmt = $conn->prepare("SELECT * FROM users WHERE employee_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    echo "User not found.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit User #<?= $user['employee_id'] ?></title>
  <link rel="icon" href="/img/nkey.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
  <div class="card mx-auto shadow" style="max-width: 600px;">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h4>Edit User #<?= $user['employee_id'] ?></h4>
      <a href="users.php" class="btn btn-secondary">⬅ Back</a>
    </div>
    <div class="card-body">
      <ul class="list-group">
        <li class="list-group-item"><strong>ID:</strong> <?= $user['employee_id'] ?></li>

        <?php
        $fields = [
          'name' => 'Name',
          'email' => 'Email',
          'phone' => 'Phone',
          'rfid_id' => 'RFID',
          'department' => 'Department',
          'manager_email' => 'Manager Email'
        ];
        foreach ($fields as $key => $label):
        ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <span><strong><?= $label ?>:</strong> <span id="<?= $key ?>Display"><?= htmlspecialchars($user[$key] ?? '') ?></span></span>
          <button class="btn btn-sm btn-outline-primary" onclick="editField('<?= $key ?>')">Edit</button>
        </li>
        <li class="list-group-item d-none" id="<?= $key ?>EditRow">
          <form onsubmit="return saveField(event, '<?= $key ?>')">
            <div class="input-group">
              <input type="text" name="<?= $key ?>" id="<?= $key ?>Input" class="form-control" value="<?= htmlspecialchars($user[$key] ?? '') ?>">
              <button class="btn btn-success" type="submit">Save</button>
              <button class="btn btn-secondary" type="button" onclick="cancelEdit('<?= $key ?>')">Cancel</button>
            </div>
          </form>
        </li>
        <?php endforeach; ?>

        <li class="list-group-item d-flex justify-content-between align-items-center">
          <span><strong>Admin:</strong> <span id="is_adminDisplay"><?= $user['is_admin'] ? 'Yes' : 'No' ?></span></span>
          <button class="btn btn-sm btn-outline-primary" onclick="editField('is_admin')">Edit</button>
        </li>
        <li class="list-group-item d-none" id="is_adminEditRow">
          <form onsubmit="return saveField(event, 'is_admin')">
            <div class="input-group">
              <select id="is_adminInput" class="form-select">
                <option value="0" <?= $user['is_admin'] == 0 ? 'selected' : '' ?>>No</option>
                <option value="1" <?= $user['is_admin'] == 1 ? 'selected' : '' ?>>Yes</option>
              </select>
              <button class="btn btn-success" type="submit">Save</button>
              <button class="btn btn-secondary" type="button" onclick="cancelEdit('is_admin')">Cancel</button>
            </div>
          </form>
        </li>

        <li class="list-group-item"><strong>Last Login:</strong> <?= htmlspecialchars($user['last_login']) ?></li>
      </ul>
    </div>
  </div>
</div>

<script>
// Show edit row
// Показать строку редактирования
function editField(field) {
  document.getElementById(field + "Display")?.parentElement.parentElement.classList.add("d-none");
  document.getElementById(field + "EditRow")?.classList.remove("d-none");
}

// Cancel edit
// Отменить редактирование
function cancelEdit(field) {
  document.getElementById(field + "EditRow")?.classList.add("d-none");
  document.getElementById(field + "Display")?.parentElement.parentElement.classList.remove("d-none");
}

// Save changes
// Сохранить изменения
function saveField(event, field) {
  event.preventDefault();
  let value = document.getElementById(field + "Input").value;
  fetch("update_profile.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body: `field=${field}&value=${encodeURIComponent(value)}&target_id=<?= $user['employee_id'] ?>`
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      document.getElementById(field + "Display").innerText = value === '1' ? 'Yes' : value === '0' ? 'No' : value;
      cancelEdit(field);
    } else {
      alert("Error: " + data.error);
    }
  })
  .catch(err => alert("Request failed: " + err));
}
</script>
</body>
</html>