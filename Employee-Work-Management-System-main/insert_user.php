<?php
// insert_user.php - Insert new user into DB
// insert_user.php - Вставить нового пользователя в базу данных

session_start();
require_once "includes/config.php";

// Only admin can add users
// Только администратор может добавлять пользователей
if (!isset($_SESSION['employee_id']) || $_SESSION['is_admin'] != 1) {
  header("Location: login.php");
  exit;
}

// Read and validate POST data
// Считать и проверить данные POST
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$rfid_id = $_POST['rfid_id'] ?? null;
$department = $_POST['department'] ?? '';
$manager_email = $_POST['manager_email'] ?? '';
$is_admin = $_POST['is_admin'] ?? 0;
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm'] ?? '';

if ($password !== $confirm || strlen($password) < 4) {
  die("Password mismatch or too short.");
}

$hash = password_hash($password, PASSWORD_DEFAULT);

try {
  $stmt = $conn->prepare("INSERT INTO users (name, email, phone, rfid_id, department, manager_email, is_admin, password_hash) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->execute([$name, $email, $phone, $rfid_id, $department, $manager_email, $is_admin, $hash]);
  
  header("Location: users.php");
  exit;
} catch (PDOException $e) {
  die("Database error: " . $e->getMessage());
}
