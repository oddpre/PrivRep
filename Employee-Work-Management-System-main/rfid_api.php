<?php
// Enable error reporting
// Включить отображение ошибок
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set timezone
// Установить временную зону
date_default_timezone_set("Europe/Oslo");

require_once "includes/config.php";

header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);
$rfid = trim($input['rfid_id'] ?? '');

if (empty($rfid)) {
    echo json_encode(["success" => false, "status" => "Missing RFID"]);
    exit;
}

// Find user by RFID
// Найти пользователя по RFID
$stmt = $conn->prepare("SELECT employee_id, name FROM users WHERE rfid_id = ?");
$stmt->execute([$rfid]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(["success" => false, "status" => "Unknown RFID"]);
    exit;
}

$employee_id = $user['employee_id'];
$name = $user['name'];
$date = date("Y-m-d");
$now = date("Y-m-d H:i:s");
$time = date("H:i");

// Check today's entry
// Проверить запись за сегодня
$stmt = $conn->prepare("SELECT id, clock_in, clock_out FROM timesheet WHERE employee_id = ? AND DATE(clock_in) = ?");
$stmt->execute([$employee_id, $date]);
$entry = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$entry) {
    // New clock-in
    $stmt = $conn->prepare("INSERT INTO timesheet (employee_id, clock_in) VALUES (?, ?)");
    $stmt->execute([$employee_id, $now]);
    echo json_encode(["success" => true, "status" => "Checked In", "name" => $name, "time" => $time]);
    exit;
} elseif (!$entry['clock_out']) {
    // Clock-out
    $stmt = $conn->prepare("UPDATE timesheet SET clock_out = ? WHERE id = ?");
    $stmt->execute([$now, $entry['id']]);
    echo json_encode(["success" => true, "status" => "Checked Out", "name" => $name, "time" => $time]);
    exit;
} else {
    // Already clocked out
    echo json_encode(["success" => true, "status" => "Already Checked Out", "name" => $name, "time" => $time]);
    exit;
}