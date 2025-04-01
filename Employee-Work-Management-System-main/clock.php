<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Clock In/Out | NKEY System</title>
  <link rel="icon" href="/img/nkey.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/css/style.css?v=1.0.0">
</head>
<body class="bg-light">

<div class="container py-5">
  <div class="card shadow mx-auto" style="max-width: 400px;">
    <div class="card-header text-center">
      <h5 class="mb-0">⏱ Clock In / Out</h5>
    </div>
    <div class="card-body">
      <!-- Clock form for entering employee ID -->
      <!-- Форма входа для ввода ID сотрудника -->
      <form id="clockForm">
        <div class="mb-3">
          <label for="emp_id" class="form-label">Enter your Employee ID</label>
          <!-- Input field for ID / Поле для ввода ID -->
          <input type="number" class="form-control" id="emp_id" name="emp_id" required>
        </div>
        <div class="d-flex justify-content-between">
          <button type="submit" class="btn btn-primary">Submit</button>
          <button type="button" class="btn btn-secondary" onclick="window.location.href='index.html'">Cancel</button>
        </div>
        <div id="errorMsg" class="mt-3 text-danger d-none">Invalid ID. Please try again.</div>
      </form>

      <!-- Confirmation box appears after ID is submitted -->
      <!-- Блок подтверждения появляется после ввода ID -->
      <form id="actionForm" action="clock_action.php" method="POST" class="mt-4 d-none">
        <input type="hidden" name="emp_id" id="hiddenEmpId">
        <p id="actionText"></p>
        <div class="d-flex justify-content-between">
          <button type="submit" class="btn btn-success">Confirm</button>
          <button type="button" id="cancelBtn" class="btn btn-secondary">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Always reset error message on load
// Всегда сбрасывать сообщение об ошибке при загрузке
window.onload = () => {
  document.getElementById("errorMsg").classList.add("d-none");
};

function getFormattedNow() {
  const now = new Date();
  const dd = String(now.getDate()).padStart(2, '0');
  const mm = String(now.getMonth() + 1).padStart(2, '0');
  const yyyy = now.getFullYear();
  const hh = String(now.getHours()).padStart(2, '0');
  const min = String(now.getMinutes()).padStart(2, '0');
  return `${dd}.${mm}.${yyyy} ${hh}:${min}`;
}

async function fetchUserName(empId) {
  try {
    const response = await fetch(`get_name.php?id=${empId}`);
    const data = await response.json();
    return data.success ? data.name : null;
  } catch {
    return null;
  }
}

// Handle submit of employee ID
// Обработка отправки ID сотрудника

document.getElementById("clockForm").addEventListener("submit", async function(e) {
  e.preventDefault();
  const empId = document.getElementById("emp_id").value;
  const name = await fetchUserName(empId);
  const nowFormatted = getFormattedNow();

  if (!name) {
    document.getElementById("errorMsg").classList.remove("d-none");
    document.getElementById("emp_id").value = "";
    document.getElementById("emp_id").focus();
    return;
  }

  const isClockedIn = Math.random() < 0.5; // Placeholder logic

  document.getElementById("clockForm").classList.add("d-none");
  document.getElementById("actionForm").classList.remove("d-none");
  document.getElementById("hiddenEmpId").value = empId;
  document.getElementById("actionText").innerText = isClockedIn ?
    `${name} is currently clocked in as of ${nowFormatted}. Do you want to clock out?` :
    `${name} is not clocked in. Clock in now at ${nowFormatted}?`;
});

// Cancel goes back to home
// Кнопка отмены возвращает на главную

document.getElementById("cancelBtn").onclick = function() {
  window.location.href = "index.html";
};
</script>
</body>
</html>