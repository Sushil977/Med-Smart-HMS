<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medsmart";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Database connection failed: " . $conn->connect_error);

$txn_code = isset($_GET['txn']) ? trim($_GET['txn']) : '';
if (empty($txn_code)) die("Invalid request.");

// Fetch appointment
$stmt = $conn->prepare("SELECT patient_name, patient_age, gender, department, contact_number, email, blood_group, appointment_date, appointment_time, transaction_code 
                        FROM appointments WHERE transaction_code = ?");
$stmt->bind_param("s", $txn_code);
$stmt->execute();
$result = $stmt->get_result();
$appointment = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$appointment) die("Appointment not found.");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Appointment Confirmation</title>
  <style>
    @page { size: A4; margin: 20mm; }
    body { font-family: Arial, sans-serif; margin: 0; padding: 20mm; color: #000; }
    .container { max-width: 800px; margin: 0 auto; border: 1px solid #333; padding: 20px 40px; box-sizing: border-box; }
    h1, h2 { text-align: center; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1.5px; }
    hr { border: none; border-top: 2px solid #333; margin: 20px 0; }
    .field-label { font-weight: bold; width: 180px; display: inline-block; }
    .field { margin: 10px 0; font-size: 16px; }
    .footer-note { margin-top: 40px; font-size: 14px; color: #555; text-align: center; font-style: italic; }
  </style>
</head>
<body>
  <div class="container">
    <h1>Med Smart Hospital</h1>
    <h2>Appointment Confirmation</h2>
    <hr />

    <div class="field"><span class="field-label">Name:</span> <?= htmlspecialchars($appointment['patient_name']) ?></div>
    <div class="field"><span class="field-label">Contact Number:</span> <?= htmlspecialchars($appointment['contact_number']) ?></div>
    <div class="field"><span class="field-label">Blood Group:</span> <?= htmlspecialchars($appointment['blood_group']) ?></div>

    <hr />

    <div class="field"><span class="field-label">Age:</span> <?= htmlspecialchars($appointment['patient_age']) ?></div>
    <div class="field"><span class="field-label">Gender:</span> <?= htmlspecialchars($appointment['gender']) ?></div>
    <div class="field"><span class="field-label">Department:</span> <?= htmlspecialchars($appointment['department']) ?></div>
    <div class="field"><span class="field-label">Email:</span> <?= htmlspecialchars($appointment['email']) ?></div>
    <div class="field"><span class="field-label">Appointment Date:</span> <?= htmlspecialchars($appointment['appointment_date']) ?></div>
    <div class="field"><span class="field-label">Appointment Time:</span> <?= htmlspecialchars($appointment['appointment_time']) ?></div>
    <div class="field"><span class="field-label">Transaction Code:</span> <?= htmlspecialchars($appointment['transaction_code']) ?></div>

    <hr />

    <div class="footer-note">
      Please arrive at least 10 minutes before your appointment time.<br />
      Appointment fee Rs. 500 is non-refundable.<br />
      Thank you for choosing Med Smart Hospital.
    </div>
  </div>
</body>
</html>
