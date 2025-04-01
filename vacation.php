<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit;
}

require_once "includes/config.php";

$employee_id = $_SESSION['employee_id'];
$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $from = $_POST['from_date'] ?? '';
    $to = $_POST['to_date'] ?? '';
    $reason = $_POST['reason'] ?? '';
    $today = date('Y-m-d');

    if ($from >= $today && $to >= $from && !empty($reason)) {
        $stmt = $conn->prepare("INSERT INTO leaves (employee_id, from_date, to_date, reason, status, date_requested) VALUES (?, ?, ?, ?, 'pending', datetime('now'))");
        $stmt->execute([$employee_id, $from, $to, $reason]);
        $message = "✅ Vacation request submitted!";
    } else {
        $message = "❌ Invalid date range or missing reason.";
    }
}

// Fetch previous requests
$stmt = $conn->prepare("SELECT from_date, to_date, reason, status, date_requested FROM leaves WHERE employee_id = ? ORDER BY date_requested DESC");
$stmt->execute([$employee_id]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Vacation</title>
  <link rel="icon" href="/img/nkey.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/css/style.css?v=1.0.0">
</head>
<body>
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">🌴 Apply for Vacation</h2>
    <a href="dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>
  </div>

  <?php if (!empty($message)): ?>
    <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <form method="post" class="card p-4 mb-5">
    <div class="row mb-3">
      <div class="col-md-4">
        <label for="from_date" class="form-label">From Date</label>
        <input type="date" name="from_date" id="from_date" class="form-control" required>
      </div>
      <div class="col-md-4">
        <label for="to_date" class="form-label">To Date</label>
        <input type="date" name="to_date" id="to_date" class="form-control" required>
      </div>
      <div class="col-md-4">
        <label for="reason" class="form-label">Reason</label>
        <input type="text" name="reason" id="reason" class="form-control" required>
      </div>
    </div>
    <button type="submit" class="btn btn-primary">Send Request</button>
  </form>

  <h4 class="mb-3">📋 My Vacation Requests</h4>
  <table class="table table-bordered">
    <thead class="table-light">
      <tr>
        <th>From</th>
        <th>To</th>
        <th>Reason</th>
        <th>Status</th>
        <th>Requested</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($requests as $row):
      $badge = 'secondary';
      if ($row['status'] === 'approved') $badge = 'success';
      elseif ($row['status'] === 'pending') $badge = 'warning';
      elseif ($row['status'] === 'declined') $badge = 'danger';
    ?>
      <tr>
        <td><?= htmlspecialchars($row['from_date']) ?></td>
        <td><?= htmlspecialchars($row['to_date']) ?></td>
        <td><?= htmlspecialchars($row['reason']) ?></td>
        <td><span class="badge bg-<?= $badge ?>"><?= ucfirst($row['status']) ?></span></td>
        <td><?= htmlspecialchars($row['date_requested']) ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>
