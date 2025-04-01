<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit;
}

require_once "includes/config.php";

$employee_id = $_SESSION['employee_id'];

$month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

$holidays = ['01.01', '01.05', '17.05', '25.12', '26.12'];

// Fetch clock in/out data
$stmt = $conn->prepare("SELECT date(clock_in) as day, clock_in, clock_out, note FROM timesheet WHERE employee_id = ? AND strftime('%Y', clock_in) = ? AND strftime('%m', clock_in) = ?");
$stmt->execute([$employee_id, $year, str_pad($month, 2, '0', STR_PAD_LEFT)]);
$clockData = $stmt->fetchAll(PDO::FETCH_ASSOC);
$clockMap = [];
foreach ($clockData as $entry) {
    $clockMap[$entry['day']] = $entry;
}

// Fetch leaves
$stmt = $conn->prepare("SELECT from_date, to_date, status FROM leaves WHERE employee_id = ? AND (
    (strftime('%Y', from_date) = ? AND strftime('%m', from_date) = ?)
    OR
    (strftime('%Y', to_date) = ? AND strftime('%m', to_date) = ?)
)");
$stmt->execute([$employee_id, $year, str_pad($month, 2, '0', STR_PAD_LEFT), $year, str_pad($month, 2, '0', STR_PAD_LEFT)]);
$leaves = $stmt->fetchAll(PDO::FETCH_ASSOC);

$leaveDays = [];
foreach ($leaves as $leave) {
    $start = new DateTime($leave['from_date']);
    $end = new DateTime($leave['to_date']);
    while ($start <= $end) {
        $key = $start->format('Y-m-d');
        $leaveDays[$key] = $leave['status'];
        $start->modify('+1 day');
    }
}

$prevMonth = $month - 1;
$prevYear = $year;
if ($prevMonth < 1) {
    $prevMonth = 12;
    $prevYear--;
}
$nextMonth = $month + 1;
$nextYear = $year;
if ($nextMonth > 12) {
    $nextMonth = 1;
    $nextYear++;
}

$currentMonth = date('m');
$currentYear = date('Y');
$disableNext = ($month == $currentMonth && $year == $currentYear);
$today = new DateTime();
$todayDateOnly = $today->format('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Timesheet</title>
  <link rel="icon" href="/img/nkey.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/css/style.css?v=1.0.0">
</head>
<body>
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">📊 My Timesheet - <?= date('F Y', strtotime("$year-$month-01")) ?></h2>
    <a href="dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>
  </div>
  <div class="mb-3 d-flex justify-content-between">
    <a href="?month=<?= $prevMonth ?>&year=<?= $prevYear ?>" class="btn btn-outline-primary">⬅ Previous</a>
    <?php if (!$disableNext): ?>
      <a href="?month=<?= $nextMonth ?>&year=<?= $nextYear ?>" class="btn btn-outline-primary">Next ➡</a>
    <?php endif; ?>
  </div>
  <table class="table table-bordered">
    <thead class="table-light">
      <tr>
        <th>Date</th>
        <th>Status</th>
        <th>Check In</th>
        <th>Check Out</th>
        <th>Note</th>
      </tr>
    </thead>
    <tbody>
      <?php for ($day = 1; $day <= $daysInMonth; $day++):
        $dateStr = sprintf("%04d-%02d-%02d", $year, $month, $day);
        $formatted = date("d.m", strtotime($dateStr));
        $weekday = date("w", strtotime($dateStr));
        $isFuture = new DateTime($dateStr) > $today;

        $rowClass = '';
        if ($weekday == 0) $rowClass = 'table-danger'; // Sunday
        elseif ($weekday == 6) $rowClass = 'table-warning'; // Saturday

        $status = "Absent";
        $in = $out = $note = "-";

        if (isset($clockMap[$dateStr])) {
            $status = "Clocked In";
            $in = date("H:i", strtotime($clockMap[$dateStr]['clock_in']));

            $clockOutRaw = $clockMap[$dateStr]['clock_out'];
            if (!empty($clockOutRaw) && strtotime($clockOutRaw) > strtotime($clockMap[$dateStr]['clock_in'])) {
                $out = date("H:i", strtotime($clockOutRaw));
            } elseif ($dateStr < $todayDateOnly) {
                $estimated = new DateTime($clockMap[$dateStr]['clock_in']);
                $estimated->add(new DateInterval("PT8H"));
                $out = $estimated->format("H:i") . " (auto)";
            }
            $note = $clockMap[$dateStr]['note'] ?? "-";
        } elseif (isset($leaveDays[$dateStr])) {
            $status = "Vacation ({$leaveDays[$dateStr]})";
        } elseif (in_array($formatted, $holidays)) {
            $status = "Holiday";
        } elseif ($isFuture) {
            $status = "Upcoming";
        }
      ?>
        <tr class="<?= $rowClass ?>">
          <td><?= date("d.m.Y", strtotime($dateStr)) ?></td>
          <td><?= $status ?></td>
          <td><?= $in ?></td>
          <td><?= $out ?></td>
          <td><?= $note ?></td>
        </tr>
      <?php endfor; ?>
    </tbody>
  </table>
</div>
</body>
</html>