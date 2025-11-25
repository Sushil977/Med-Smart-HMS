<?php
session_start();
$conn = new mysqli("localhost", "root", "", "medsmart");
if ($conn->connect_error) die("DB failed: " . $conn->connect_error);

$appointment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch appointment
$stmt = $conn->prepare("SELECT * FROM appointments WHERE id=?");
$stmt->bind_param("i", $appointment_id);
$stmt->execute();
$appointment = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$appointment) die("Appointment not found.");

// Handle form submission
if(isset($_POST['reschedule'])) {
    $new_date = $_POST['appointment_date'];
    $new_time = $_POST['appointment_time'];

    // Check for time conflicts
    $doctor_id = $appointment['doctor_id'];
    $appointment_duration = 30;

    $stmt = $conn->prepare("
        SELECT COUNT(*) as cnt 
        FROM appointments 
        WHERE doctor_id = ? 
          AND appointment_date = ? 
          AND TIMESTAMPDIFF(MINUTE, appointment_time, ?) BETWEEN -? AND ?
          AND status != 'cancelled'
          AND id != ?
    ");
    $stmt->bind_param("issiii", $doctor_id, $new_date, $new_time, $appointment_duration, $appointment_duration, $appointment_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if($result['cnt'] > 0){
        echo "<script>alert('Doctor already has an appointment at this time. Choose another slot.'); window.history.back();</script>";
        exit;
    }

    // Update appointment
    $stmt = $conn->prepare("UPDATE appointments SET appointment_date=?, appointment_time=?, status='rescheduled' WHERE id=?");
    $stmt->bind_param("ssi", $new_date, $new_time, $appointment_id);
    if($stmt->execute()){
        echo "<script>alert('Appointment rescheduled successfully.'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Failed to reschedule.'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Reschedule Appointment</title>
</head>
<body>
<h2>Reschedule Appointment</h2>
<form method="POST">
    <label>Date:</label>
    <input type="date" name="appointment_date" value="<?= $appointment['appointment_date'] ?>" required><br>
    <label>Time:</label>
    <input type="time" name="appointment_time" value="<?= $appointment['appointment_time'] ?>" required><br>
    <button type="submit" name="reschedule">Reschedule</button>
</form>
</body>
</html>
