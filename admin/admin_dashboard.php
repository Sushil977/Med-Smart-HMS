<?php 
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "medsmart");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$result = $conn->query("SELECT * FROM appointments ORDER BY appointment_date DESC, appointment_time DESC");
?>
<!DOCTYPE html>
<html>
<head>
<title>Appointments - MedSmart</title>
<style>
body { margin:0; font-family:Arial; background:#f7f7f7; display:flex; }
.sidebar { width:220px; background:#007bff; color:white; height:100vh; position:fixed; padding-top:30px; }
.sidebar h2 { text-align:center; }
.sidebar a { color:white; text-decoration:none; display:block; padding:15px; }
.main-content { margin-left:220px; padding:20px; width:calc(100% - 220px); }
table { width:100%; border-collapse:collapse; background:white; }
th { background:#007bff; color:white; padding:12px; }
td { padding:12px; border-bottom:1px solid #ddd; }
</style>
</head>
<body>

<div class="sidebar">
<h2>Med-Smart</h2>
<a href="admin_dashboard.php">Dashboard</a>
<a href="doctors.php">Doctors</a>
<a href="patients.php">Patients</a>
<a href="admin_logout.php">Logout</a>
</div>

<div class="main-content">
<h1>Appointments</h1>

<table>
<thead>
<tr>
<th>ID</th>
<th>Name</th>
<th>Age</th>
<th>Gender</th>
<th>Department</th>
<th>Contact</th>
<th>Blood</th>
<th>Date</th>
<th>Time</th>
<th>Doctor</th>
<th>Status</th>
<th>Report</th>
<th>Doctor Report</th>
</tr>
</thead>

<tbody>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= htmlspecialchars($row['patient_name']) ?></td>
<td><?= $row['patient_age'] ?></td>
<td><?= $row['gender'] ?></td>
<td><?= $row['department'] ?></td>
<td><?= $row['contact_number'] ?></td>
<td><?= $row['blood_group'] ?></td>
<td><?= $row['appointment_date'] ?></td>
<td><?= $row['appointment_time'] ?></td>
<td><?= $row['doctor_id'] ?></td>
<td><?= $row['status'] ?></td>

<td>
<?php if(!empty($row['report_path'])): ?>
<a href="../patient/<?= $row['report_path'] ?>" target="_blank">View</a>
<?php else: ?>No Report<?php endif; ?>
</td>

<td>
<?php if(!empty($row['doctor_report_path'])): ?>
<a href="../patient/<?= $row['doctor_report_path'] ?>" target="_blank">View</a>
<?php else: ?>No Report<?php endif; ?>
</td>

</tr>
<?php endwhile; ?>
</tbody>

</table>
</div>

</body>
</html>
