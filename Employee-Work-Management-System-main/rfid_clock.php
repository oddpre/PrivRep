<?php
// Enable error reporting
// Включить отображение ошибок
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set timezone
// Установить временную зону
date_default_timezone_set("Europe/Oslo");

require_once "includes/config.php";

$rfid = trim($_POST['rfid_id'] ?? '');
file_put_contents("debug.log", "RFID input: [$rfid]\n", FILE_APPEND); // Debug log

if (empty($rfid)) {
    header("Location: rfid_terminal.html");
    exit;
}

// Find user by RFID
// Найти пользователя по RFID
$stmt = $conn->prepare("SELECT employee_id, name FROM users WHERE rfid_id = ?");
$stmt->execute([$rfid]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    file_put_contents("debug.log", "User not found for RFID: [$rfid]\n", FILE_APPEND); // Debug log
    echo "<script>alert('Unknown RFID'); setTimeout(() => window.location.href = 'rfid_terminal.html', 3000);</script>";
    exit;
}

$employee_id = $user['employee_id'];
$name = htmlspecialchars($user['name']);

// Check today's entry
// Проверить запись за сегодня
$date = date("Y-m-d");
$now = date("Y-m-d H:i:s");

$stmt = $conn->prepare("SELECT id, clock_in, clock_out FROM timesheet WHERE employee_id = ? AND DATE(clock_in) = ?");
$stmt->execute([$employee_id, $date]);
$entry = $stmt->fetch(PDO::FETCH_ASSOC);

$action = "";
$time = date("H:i");

if (!$entry) {
    // New clock-in
    // Новый вход
    $stmt = $conn->prepare("INSERT INTO timesheet (employee_id, clock_in) VALUES (?, ?)");
    $stmt->execute([$employee_id, $now]);
    $action = "Checked In"; // Зарегистрирован вход
} elseif (!$entry['clock_out']) {
    // Clock-out
    // Выход
    $stmt = $conn->prepare("UPDATE timesheet SET clock_out = ? WHERE id = ?");
    $stmt->execute([$now, $entry['id']]);
    $action = "Checked Out"; // Зарегистрирован выход
} else {
    $action = "Already Clocked Out"; // Уже зарегистрирован выход
}

// Show message and redirect
// Показать сообщение и перенаправить
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RFID Confirmation</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #111;
      color: white;
      font-family: sans-serif;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      height: 100vh;
      text-align: center;
    }
    h1 { font-size: 3rem; }
    p { font-size: 1.5rem; }
  </style>
</head>
<body>
  <h1><?= $action ?></h1>
  <p><?= $name ?> (<?= $employee_id ?>)</p>
  <p><?= $time ?></p>

  <script>
    setTimeout(() => window.location.href = "rfid_terminal.html", 3000);
  </script>
</body>
</html>