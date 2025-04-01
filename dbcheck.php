<?php
// dbcheck.php - Simple SQLite connection test
// dbcheck.php - Простой тест подключения к SQLite

// Enable error reporting for debugging
// Включить отображение ошибок для отладки
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define path to the database file
// Определить путь к файлу базы данных
define('DB_PATH', __DIR__ . '/db/users.db');

try {
    // Try to connect to SQLite database
    // Попытка подключиться к базе данных SQLite
    $conn = new PDO('sqlite:' . DB_PATH);

    // Set error mode to exceptions
    // Установить режим ошибок как исключения
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Run a simple query
    // Выполнить простой запрос
    $stmt = $conn->query("SELECT name FROM sqlite_master WHERE type='table'");

    // Fetch result
    // Получить результат
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Output result
    // Вывести результат
    echo "<h3>✅ Database connection successful!</h3>";
    echo "<p>Found tables: " . implode(', ', $tables) . "</p>";

} catch (PDOException $e) {
    // Show error if connection fails
    // Показать ошибку, если подключение не удалось
    echo "<h3>❌ Connection failed:</h3>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
?>
