<?php
session_start();
$conn = new mysqli("localhost", "root", "", "medsmart");
if ($conn->connect_error) die("DB failed: " . $conn->connect_error);

// Get doctor info
$doctor_id = isset($_GET['doctor_id']) ? intval($_GET['doctor_id']) : 0;

$stmt = $conn->prepare("SELECT name, specialization, duty_start, duty_end, duty_date FROM doctors WHERE id=?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$doctor = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$doctor) die("Doctor not found.");

// Format duty date & time
$duty_date = $doctor['duty_date'];
$duty_start = $doctor['duty_start'];
$duty_end = $doctor['duty_end'];

// Payment details (eSewa)
$amount = 100;        // Base amount
$tax_amount = 10;     // Tax
$total_amount = $amount + $tax_amount;
$transaction_uuid = 'appt_' . time() . '-' . bin2hex(random_bytes(4)); // Unique UUID
$product_code = "EPAYTEST";
$success_url = "https://developer.esewa.com.np/success";
$failure_url = "https://developer.esewa.com.np/failure";
$secret_key = "8gBm/:&EnhH.1/q"; // Provided for UAT

// Create signature using HMAC SHA-256 and Base64
$fields = [
    'total_amount' => $total_amount,
    'transaction_uuid' => $transaction_uuid,
    'product_code' => $product_code
];
$data_string = "total_amount={$fields['total_amount']},transaction_uuid={$fields['transaction_uuid']},product_code={$fields['product_code']}";
$signature = base64_encode(hash_hmac('sha256', $data_string, $secret_key, true));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Book Appointment</title>
<style>
body { font-family: 'Segoe UI', sans-serif; background: #f4f9ff; margin:0; padding:0; }
.container { width: 90%; max-width: 500px; margin: 40px auto; background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
h2 { text-align: center; color: #2c3e50; margin-bottom: 20px; }
label { font-weight: bold; display: block; margin-top: 15px; }
input, select { width: 100%; padding: 10px; margin-top: 5px; border-radius: 6px; border:1px solid #ccc; font-size: 15px; }
button { margin-top: 20px; width: 100%; padding: 12px; background: #007bff; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; }
button:hover { background: #0056b3; }
.notice { background: #eaf6ff; padding: 10px; border-left: 4px solid #007bff; margin-bottom: 15px; font-size: 14px; }
.flex-btns { display: flex; gap: 10px; justify-content: space-between; margin-top: 10px; }
.flex-btns button { flex: 1; }
.payment-btn { background: #28a745; }
</style>
</head>
<body>

<div class="container">
<h2>Book Appointment</h2>

<p class="notice">
<strong>Doctor:</strong> <?= htmlspecialchars($doctor['name']) ?><br>
<strong>Department:</strong> <?= htmlspecialchars($doctor['specialization']) ?><br>
<strong>Duty Date:</strong> <?= htmlspecialchars($duty_date) ?><br>
<strong>Duty Time:</strong> <?= htmlspecialchars($duty_start) ?> to <?= htmlspecialchars($duty_end) ?>
</p>

<form method="POST" action="appointment.php" enctype="multipart/form-data">
<input type="hidden" name="doctor_id" value="<?= $doctor_id ?>">
<input type="hidden" name="doctor_name" value="<?= htmlspecialchars($doctor['name']) ?>">
<input type="hidden" name="department" value="<?= htmlspecialchars($doctor['specialization']) ?>">

<label>Full Name</label>
<input type="text" name="name" required>

<label>Age</label>
<input type="number" name="age" required>

<label>Gender</label>
<select name="gender" required>
<option value="">Select Gender</option>
<option>Male</option>
<option>Female</option>
<option>Other</option>
</select>

<label>Contact Number</label>
<input type="text" name="contact_number" required>

<label>Email</label>
<input type="email" name="email">

<label>Blood Group</label>
<select name="blood_group">
<option value="">Select Blood Group</option>
<option>A+</option>
<option>A-</option>
<option>B+</option>
<option>B-</option>
<option>O+</option>
<option>O-</option>
<option>AB+</option>
<option>AB-</option>
</select>

<label>Appointment Date</label>
<input type="date" name="appointment_date" required min="<?= $duty_date ?>" max="<?= $duty_date ?>">

<label>Appointment Time</label>
<input type="time" name="appointment_time" required min="<?= $duty_start ?>" max="<?= $duty_end ?>">

<label>Upload Report (optional)</label>
<input type="file" name="patient_image">

<div class="flex-btns">
    <button type="submit" name="book">Book Appointment</button>
    <button type="button" class="payment-btn" onclick="document.getElementById('esewaForm').submit();">Pay Now</button>
</div>
</form>

<!-- Hidden eSewa Form -->
<form action="https://rc-epay.esewa.com.np/api/epay/main/v2/form" method="POST" id="esewaForm" style="display:none;">
    <input type="text" name="amount" value="<?= $amount ?>" required>
    <input type="text" name="tax_amount" value="<?= $tax_amount ?>" required>
    <input type="text" name="total_amount" value="<?= $total_amount ?>" required>
    <input type="text" name="transaction_uuid" value="<?= $transaction_uuid ?>" required>
    <input type="text" name="product_code" value="<?= $product_code ?>" required>
    <input type="text" name="product_service_charge" value="0" required>
    <input type="text" name="product_delivery_charge" value="0" required>
    <input type="text" name="success_url" value="<?= $success_url ?>" required>
    <input type="text" name="failure_url" value="<?= $failure_url ?>" required>
    <input type="text" name="signed_field_names" value="total_amount,transaction_uuid,product_code" required>
    <input type="text" name="signature" value="<?= $signature ?>" required>
</form>

</div>
</body>
</html>
