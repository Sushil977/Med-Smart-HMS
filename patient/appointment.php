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

$transaction_code = "TRX" . time();
$status = "pending";

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

// doctor report empty for now
$doctor_report_path = "";

// Insert Query
$sql = "
INSERT INTO appointments
(patient_name, patient_age, gender, department, contact_number, email, blood_group,
 transaction_code, appointment_date, appointment_time, doctor_id, status, created_at,
 report_path, doctor_report_path)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "sissssssssisss",
    $name,
    $age,
    $gender,
    $department,
    $contact,
    $email,
    $blood,
    $transaction_code,
    $appointment_date,
    $appointment_time,
    $doctor_id,
    $status,
    $report_path,
    $doctor_report_path
);

if ($stmt->execute()) {
    // Redirect to patient's dashboard after booking
    echo "<script>alert('Appointment booked successfully!'); window.location.href='dashboard.php';</script>";
} else {
    echo "<script>alert('Failed to book appointment!'); window.history.back();</script>";
}

?>
