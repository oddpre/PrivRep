<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once "includes/config.php";

$stmt = $conn->query("SELECT employee_id, name FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>";
print_r($users);
echo "</pre>";
