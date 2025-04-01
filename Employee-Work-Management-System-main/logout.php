<?php
// Start the session
// Запустить сессию
session_start();

// Unset all session variables
// Удалить все переменные сессии
$_SESSION = [];

// Destroy the session
// Уничтожить сессию
session_destroy();

// Redirect to login page
// Перенаправить на страницу входа
header("Location: index.html");
exit;
?>
