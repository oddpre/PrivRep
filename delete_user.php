<?php
// delete_user.php - Delete a user from the system (admin only)
// delete_user.php - Удалить пользователя из системы (только для администратора)

session_start();

// Check if user is logged in and is admin
// Проверить, авторизован ли пользователь и является ли администратором
if (!isset($_SESSION["employee_id"]) || $_SESSION["is_admin"] != 1) {
    header("Location: login.php");
    exit;
}

require_once "includes/config.php";

// Check if ID is set
// Проверить, передан ли ID
if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit;
}

$employee_id = $_GET['id'];

// Prevent deletion of self
// Запретить удаление самого себя
if ($employee_id == $_SESSION['employee_id']) {
    header("Location: users.php?error=self-delete");
    exit;
}

// Delete user from database
// Удалить пользователя из базы данных
$stmt = $conn->prepare("DELETE FROM users WHERE employee_id = :id");
$stmt->bindParam(':id', $employee_id, PDO::PARAM_INT);
$stmt->execute();

// Redirect back
// Перенаправить обратно
header("Location: users.php?success=deleted");
exit;
