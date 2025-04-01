<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start the session
// Запустить сессию
session_start();

// Include database connection file
// Подключить файл соединения с базой данных
// require_once "includes/config.php"; LATER

// Optional: Include additional functions
// Необязательно: Подключить дополнительные функции
require_once "includes/functions.php";
require_once "includes/config.php";

// Initialize message variable
// Инициализировать переменную сообщения
$message = "";

// Check if form is submitted via POST
// Проверить, была ли форма отправлена через POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Get employee ID from form input
    // Получить ID сотрудника из формы
    $employee_id = $_POST["employee_id"];

    // Get password from form input
    // Получить пароль из формы
    $password = $_POST["password"];

    // Prepare SQL query to find user by employee ID
    // Подготовить SQL-запрос для поиска пользователя по ID
    $stmt = $conn->prepare("SELECT * FROM users WHERE employee_id = ?");
    $stmt->execute([$employee_id]);

    // Fetch user data as associative array
    // Получить данные пользователя в виде ассоциативного массива
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if user exists and password is valid
    // Проверить, существует ли пользователь и верный ли пароль
    if ($user && password_verify($password, $user["password_hash"])) {

        // Store employee info in session variables
        // Сохранить информацию о пользователе в переменных сессии
        $_SESSION["employee_id"] = $user["employee_id"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["is_admin"] = $user["is_admin"];

        // Redirect to dashboard page
        // Перенаправить на страницу панели управления
        header("Location: dashboard.php");
        exit;

    } else {
        // If login fails, show error message
        // Если вход не удался, показать сообщение об ошибке
        $message = "Invalid employee ID or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | NKEY System</title>
  <link rel="icon" href="/img/nkey.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/css/style.css?v=1.0.0">
</head>
<body class="bg-light">

  <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
    <div class="card shadow p-4" style="min-width: 350px;">
      <h3 class="mb-3 text-center">Employee Login</h3>

      <?php if ($message): ?>
        <!-- Show error message if login failed -->
        <!-- Показать сообщение об ошибке, если вход не удался -->
        <div class="alert alert-danger"><?= $message ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="mb-3">
          <label for="employee_id" class="form-label">Employee ID</label>
          <!-- Input for employee ID -->
          <!-- Поле ввода ID сотрудника -->
          <input type="number" class="form-control" name="employee_id" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <!-- Input for password -->
          <!-- Поле ввода пароля -->
          <input type="password" class="form-control" name="password" required>
        </div>
        <!-- Submit button -->
        <!-- Кнопка отправки -->
        <button type="submit" class="btn btn-primary w-100">Login</button>
      </form>
    </div>
  </div>

</body>
</html>
