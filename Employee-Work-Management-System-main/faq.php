<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FAQ | NKEY System</title>
  <link rel="icon" href="/img/nkey.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/css/style.css?v=1.0.0">
</head>
<body>
  <div class="container py-5">
    <h2 class="mb-4">❓ Frequently Asked Questions</h2>

    <div class="accordion" id="faqAccordion">
      <div class="accordion-item">
        <h2 class="accordion-header" id="q1">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#a1" aria-expanded="true">
            How do I clock in or out?
          </button>
        </h2>
        <div id="a1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">\          <div class="accordion-body">
            Use the "⏱ Clock In/Out" button on the dashboard or front page. If you forget to clock out, the system automatically sets clock out to 8 hours later, only if the day has passed.
          </div>
        </div>
      </div>

      <div class="accordion-item">
        <h2 class="accordion-header" id="q2">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a2">
            Why does my timesheet show "Absent"?
          </button>
        </h2>
        <div id="a2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            You are marked as absent if there is no clock-in or vacation request for that day. Sundays are never marked absent.
          </div>
        </div>
      </div>

      <div class="accordion-item">
        <h2 class="accordion-header" id="q3">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a3">
            How do I apply for vacation?
          </button>
        </h2>
        <div id="a3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            Go to "🌴 My Vacation" from the menu. Select the date range and write a short reason. Your request will be marked as pending until admin reviews it.
          </div>
        </div>
      </div>

      <div class="accordion-item">
        <h2 class="accordion-header" id="q4">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a4">
            What does "Upcoming" mean in the timesheet?
          </button>
        </h2>
        <div id="a4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            Upcoming means the day has not happened yet. It is used instead of "Absent" for future dates.
          </div>
        </div>
      </div>

      <div class="accordion-item">
        <h2 class="accordion-header" id="q5">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a5">
            What happens if I forget to clock out?
          </button>
        </h2>
        <div id="a5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            If the current day has passed and you didn’t clock out, the system sets your logout time to 8 hours after your login time, but only after 23:59 that day.
          </div>
        </div>
      </div>
    </div>

    <div class="mt-5">
      <a href="index.html" class="btn btn-secondary">⬅ Back to Home</a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
