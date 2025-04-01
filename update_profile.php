<?php
// update_profile.php - Save profile changes (user or admin)
// update_profile.php - Сохранение изменений профиля (пользователь или администратор)

session_start();

// Load database connection
// Загрузить подключение к базе данных
require_once "includes/config.php";

header('Content-Type: application/json');

// Check if logged in
// Проверка авторизации
if (!isset($_SESSION["employee_id"])) {
  echo json_encode(["success" => false, "error" => "Not logged in"]);
  exit;
}

// Get data from request
// Получить данные из запроса
$field = $_POST['field'] ?? '';
$value = $_POST['value'] ?? '';
$target_id = $_POST['target_id'] ?? $_SESSION['employee_id'];

// Only admins can edit other users
// Только админы могут изменять чужие данные
if ($target_id != $_SESSION['employee_id'] && $_SESSION['is_admin'] != 1) {
  echo json_encode(["success" => false, "error" => "Unauthorized"]);
  exit;
}

$allowed_fields = ["name", "phone", "rfid_id", "password", "email", "department", "manager_email", "is_admin"];
if (!in_array($field, $allowed_fields)) {
  echo json_encode(["success" => false, "error" => "Invalid field"]);
  exit;
}

try {
  if ($field === "password") {
    if (strlen($value) < 4) throw new Exception("Password too short");
    $value = password_hash($value, PASSWORD_DEFAULT);
  }
  if ($field === "is_admin") {
    $value = $value === "1" ? 1 : 0;
  }

  $stmt = $conn->prepare("UPDATE users SET $field = :value WHERE employee_id = :id");
  $stmt->execute([
    ":value" => $value,
    ":id" => $target_id
  ]);

  echo json_encode(["success" => true]);
} catch (Exception $e) {
  echo json_encode(["success" => false, "error" => $e->getMessage()]);
  exit;
}
