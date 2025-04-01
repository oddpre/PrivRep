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

// Get all users
$users = $conn->query("SELECT employee_id, name FROM users")->fetchAll(PDO::FETCH_ASSOC);

// Get all timesheet entries
$timesheet = $conn->query("SELECT * FROM timesheet ORDER BY clock_in DESC")->fetchAll(PDO::FETCH_ASSOC);

// Get all approved leaves
$leaves = $conn->query("SELECT employee_id, from_date, to_date FROM leaves WHERE status = 'approved'")->fetchAll(PDO::FETCH_ASSOC);

// Build vacation lookup table
$vacation_map = [];
foreach ($leaves as $leave) {
    $start = new DateTime($leave['from_date']);
    $end = new DateTime($leave['to_date']);
    while ($start <= $end) {
        $vacation_map[$leave['employee_id']][$start->format('Y-m-d')] = true;
        $start->modify('+1 day');
    }
}

// Organize timesheet by user and date
$logins = [];
foreach ($timesheet as $entry) {
    $date = substr($entry['clock_in'], 0, 10);
    $logins[$entry['employee_id']][$date] = $entry;
}

function isWeekend($date) {
    $day = date('w', strtotime($date));
    return $day == 0 ? 'sunday' : ($day == 6 ? 'saturday' : '');
}

// Date range
$start_date = (new DateTime("-14 days"))->format('Y-m-d');
$end_date = (new DateTime("+7 days"))->format('Y-m-d');
$period = new DatePeriod(new DateTime($start_date), new DateInterval('P1D'), (new DateTime($end_date))->modify('+1 day'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Timesheet</title>
  <link rel="icon" href="/img/nkey.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/css/style.css?v=1.0.0">
</head>
<body>
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">📅 Admin Timesheet Overview</h2>
    <a href="dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>
  </div>
  <?php foreach ($users as $user): ?>
    <h4 class="mt-5 mb-3">👤 <?= htmlspecialchars($user['name']) ?> (ID: <?= $user['employee_id'] ?>)</h4>
    <table class="table table-bordered">
      <thead class="table-light">
        <tr>
          <th>Date</th>
          <th>Status</th>
          <th>Check In</th>
          <th>Check Out</th>
          <th>Hours</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($period as $date): 
        $d = $date->format('Y-m-d');
        $row = $logins[$user['employee_id']][$d] ?? null;
        $status = '-'; $in = '-'; $out = '-'; $hours = '-';
        $today = date('Y-m-d');
        $style = '';

        if (isset($vacation_map[$user['employee_id']][$d])) {
          $status = 'Vacation';
        } elseif ($row) {
          $status = 'Clocked In';
          $in = date('H:i', strtotime($row['clock_in']));
          if ($row['clock_out']) {
              $out = date('H:i', strtotime($row['clock_out']));
              $hours = round((strtotime($row['clock_out']) - strtotime($row['clock_in'])) / 3600, 2);
          } else if ($d < $today) {
              $out = date('H:i', strtotime($row['clock_in']) + 8 * 3600);
              $hours = 8;
          } else {
              $out = '-';
          }
        } elseif ($d > $today) {
          $status = 'Upcoming';
        } else {
          $status = 'Absent';
        }

        $dayType = isWeekend($d);
        if ($dayType === 'sunday') $style = 'table-danger';
        elseif ($dayType === 'saturday') $style = 'table-warning';
      ?>
        <tr class="<?= $style ?>">
          <td><?= $d ?></td>
          <td><?= $status ?></td>
          <td><?= $in ?></td>
          <td><?= $out ?></td>
          <td><?= $hours ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endforeach; ?>
</div>
</body>
</html>