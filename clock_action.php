<?php
// Enable error reporting
// Включить отображение ошибок
ini_set("display_errors", 1);
error_reporting(E_ALL);

// Start session
// Запустить сессию
session_start();

// Include database config
// Подключить конфигурацию базы данных
require_once "includes/config.php";

// Get employee ID from POST
// Получить ID сотрудника из POST
$emp_id = $_POST["emp_id"] ?? null;

if (!$emp_id) {
    // Redirect to index if no ID provided
    // Перенаправить на index, если ID не предоставлен
    header("Location: index.html");
    exit;
}

// Get current time and date
// Получить текущее время и дату
date_default_timezone_set("Europe/Oslo");
$now = new DateTime();
$currentDate = $now->format("Y-m-d");
$currentTime = $now->format("H:i:s");

// Check if the user is already clocked in today without clocking out
// Проверить, вошел ли пользователь сегодня без выхода
$stmt = $conn->prepare("SELECT clock_in FROM timesheet WHERE employee_id = ? AND DATE(clock_in) = ? AND clock_out IS NULL");
$stmt->execute([$emp_id, $currentDate]);
$entry = $stmt->fetch(PDO::FETCH_ASSOC);

if ($entry) {
    // If clocked in → calculate default clock_out (8 hours later)
    // Если вошел → вычислить время выхода (на 8 часов позже)
    $clockIn = new DateTime($entry["clock_in"]);
    $defaultOut = $clockIn->add(new DateInterval("PT8H"))->format("Y-m-d H:i:s");

    // Update record with default 8-hour clock_out
    // Обновить запись временем выхода через 8 часов
    $stmt = $conn->prepare("UPDATE timesheet SET clock_out = ? WHERE employee_id = ? AND DATE(clock_in) = ? AND clock_out IS NULL");
    $stmt->execute([$defaultOut, $emp_id, $currentDate]);
} else {
    // Not clocked in → insert clock_in now
    // Не вошел → добавить вход сейчас
    $stmt = $conn->prepare("INSERT INTO timesheet (employee_id, clock_in) VALUES (?, ?)");
    $stmt->execute([$emp_id, $now->format("Y-m-d H:i:s")]);
}

// Redirect to index
// Перенаправить на index
header("Location: index.html");
exit;
?>
