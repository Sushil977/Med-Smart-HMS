<?php
session_start();
$conn = new mysqli("localhost", "root", "", "medsmart");
if ($conn->connect_error) die("DB failed: " . $conn->connect_error);

// Receive data
$name = $_POST['name'];
$age = $_POST['age'];
$gender = $_POST['gender'];
$contact = $_POST['contact_number'];
$email = $_POST['email'];
$blood = $_POST['blood_group'];
$appointment_date = $_POST['appointment_date'];
$appointment_time = $_POST['appointment_time'];
$doctor_id = intval($_POST['doctor_id']);
$department = $_POST['department'];

// Fetch doctor duty
$stmt = $conn->prepare("SELECT duty_start, duty_end, duty_date FROM doctors WHERE id=?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$doctor = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$doctor) die("Doctor not found.");
if ($appointment_date != $doctor['duty_date']) {
    echo "<script>alert('Invalid date! Appointment must be on the doctor\\'s duty date.'); window.history.back();</script>";
    exit;
}
if ($appointment_time < $doctor['duty_start'] || $appointment_time > $doctor['duty_end']) {
    echo "<script>alert('Invalid time! Appointment must be within the doctor\\'s duty hours.'); window.history.back();</script>";
    exit;
}

// Check for time conflicts
$appointment_duration = 30;
$stmt = $conn->prepare("
    SELECT COUNT(*) as cnt 
    FROM appointments 
    WHERE doctor_id = ? 
      AND appointment_date = ? 
      AND TIMESTAMPDIFF(MINUTE, appointment_time, ?) BETWEEN -? AND ?
      AND status != 'cancelled'
");
$stmt->bind_param("issii", $doctor_id, $appointment_date, $appointment_time, $appointment_duration, $appointment_duration);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$stmt->close();
if ($result['cnt'] > 0) {
    echo "<script>alert('Doctor already has an appointment at this time. Choose another slot.'); window.history.back();</script>";
    exit;
}

// Upload handling
$target_dir = "uploads/";
if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

$report_path = "";
if (!empty($_FILES["patient_image"]["name"])) {
    $file_name = time() . "_" . basename($_FILES["patient_image"]["name"]);
    $target_file = $target_dir . $file_name;
    if (move_uploaded_file($_FILES["patient_image"]["tmp_name"], $target_file)) {
        $report_path = $target_file;
    }
}

// Insert appointment
$transaction_code = "TRX" . time();
$status = "pending";
$doctor_report_path = "";

$stmt = $conn->prepare("
INSERT INTO appointments
(patient_name, patient_age, gender, department, contact_number, email, blood_group,
 transaction_code, appointment_date, appointment_time, doctor_id, status, created_at,
 report_path, doctor_report_path)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)
");
$stmt->bind_param("sissssssssisss",
    $name, $age, $gender, $department, $contact, $email, $blood,
    $transaction_code, $appointment_date, $appointment_time, $doctor_id,
    $status, $report_path, $doctor_report_path
);

if ($stmt->execute()) {
    echo "<script>alert('Appointment booked successfully!'); window.location.href='dashboard.php';</script>";
} else {
    echo "<script>alert('Failed to book appointment!'); window.history.back();</script>";
}
?>
