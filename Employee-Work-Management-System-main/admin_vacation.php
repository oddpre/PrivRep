<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['employee_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit;
}

require_once "includes/config.php";

// Handle approval or decline
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['leave_id'])) {
    $action = $_POST['action'];
    $leave_id = (int)$_POST['leave_id'];
    if (in_array($action, ['approved', 'declined'])) {
        $stmt = $conn->prepare("UPDATE leaves SET status = ? WHERE leave_id = ?");
        $stmt->execute([$action, $leave_id]);
    }
}

// Fetch all vacation requests
$stmt = $conn->query("SELECT l.leave_id, l.employee_id, l.from_date, l.to_date, l.reason, l.status, l.date_requested, u.name FROM leaves l JOIN users u ON l.employee_id = u.employee_id ORDER BY l.date_requested DESC");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Vacation Requests</title>
  <link rel="icon" href="/img/nkey.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/css/style.css?v=1.0.0">
</head>
<body>
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">📝 Vacation Requests (Admin)</h2>
    <a href="dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>
  </div>

  <table class="table table-bordered">
    <thead class="table-light">
      <tr>
        <th>Employee</th>
        <th>From</th>
        <th>To</th>
        <th>Reason</th>
        <th>Status</th>
        <th>Requested</th>
        <th>Actions</th>
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
        <td><?= htmlspecialchars($row['name']) ?> (ID: <?= $row['employee_id'] ?>)</td>
        <td><?= htmlspecialchars($row['from_date']) ?></td>
        <td><?= htmlspecialchars($row['to_date']) ?></td>
        <td><?= htmlspecialchars($row['reason']) ?></td>
        <td><span class="badge bg-<?= $badge ?>"><?= ucfirst($row['status']) ?></span></td>
        <td><?= htmlspecialchars($row['date_requested']) ?></td>
        <td>
          <?php if ($row['status'] === 'pending'): ?>
            <form method="post" class="d-inline">
              <input type="hidden" name="leave_id" value="<?= $row['leave_id'] ?>">
              <button type="submit" name="action" value="approved" class="btn btn-success btn-sm">Approve</button>
            </form>
            <form method="post" class="d-inline">
              <input type="hidden" name="leave_id" value="<?= $row['leave_id'] ?>">
              <button type="submit" name="action" value="declined" class="btn btn-danger btn-sm">Decline</button>
            </form>
          <?php else: ?>
            <em>No actions</em>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>