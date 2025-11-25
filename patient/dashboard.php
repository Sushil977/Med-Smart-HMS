<?php
session_start();
$conn = new mysqli("localhost", "root", "", "medsmart");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Redirect if not logged in
if(!isset($_SESSION['patient_id'])){
    header("Location: login.php");
    exit;
}

$patient_id = $_SESSION['patient_id'];
$update_message = "";

// Handle update request
if(isset($_POST['update_info'])){
    $name = $conn->real_escape_string($_POST['name']);
    $age = intval($_POST['age']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $contact = $conn->real_escape_string($_POST['contact']);
    $blood = $conn->real_escape_string($_POST['blood']);

    $sql = "UPDATE patients 
            SET name='$name', age='$age', gender='$gender', contact_number='$contact', blood_group='$blood' 
            WHERE id='$patient_id'";

    if($conn->query($sql)){
        $update_message = "Information updated successfully!";
        $_SESSION['patient_name'] = $name;
        $_SESSION['patient_age'] = $age;
        $_SESSION['patient_gender'] = $gender;
        $_SESSION['patient_contact'] = $contact;
        $_SESSION['patient_blood'] = $blood;
    } else {
        $update_message = "Failed to update information!";
    }
}

// Fetch all appointments for this patient
$stmt = $conn->prepare("
    SELECT a.*, d.name AS doctor_name 
    FROM appointments a 
    LEFT JOIN doctors d ON a.doctor_id = d.id 
    WHERE a.patient_name = ? 
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
");
$stmt->bind_param("s", $_SESSION['patient_name']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Patient Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
<style>
body { font-family: Arial, sans-serif; margin:0; padding:0; background: #f4f9ff; }
.top-header { background-color: #007bff; padding: 15px 20px; color: white; display: flex; justify-content: space-between; align-items: center; }
.top-header a { color: white; text-decoration: none; margin-left: 15px; font-weight: bold; }
.container { max-width: 1000px; margin: 30px auto; padding: 20px; background: #fff; border-radius: 12px; box-shadow: 0 6px 15px rgba(0,0,0,0.1); }
h2 { color: #007bff; margin-bottom: 20px; }
form label { display: block; margin-top: 15px; font-weight: bold; }
form input, form select { width: 100%; padding: 8px; margin-top: 5px; border-radius: 6px; border: 1px solid #ccc; }
form button { margin-top: 20px; padding: 10px 15px; border: none; border-radius: 6px; background-color: #007bff; color: white; cursor: pointer; font-weight: bold; }
form button:hover { background-color: #0056b3; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
table th, table td { border: 1px solid #ccc; padding: 10px; text-align: left; }
table th { background-color: #007bff; color: white; }
p.message { color: green; font-weight: bold; text-align: center; }
a.report-link { color: #28a745; font-weight: bold; text-decoration: none; }
a.report-link:hover { text-decoration: underline; }
.prescription-box { white-space: pre-line; padding: 5px; background: #f1faff; border-left: 4px solid #007bff; }
</style>
</head>
<body>

<div class="top-header">
    <div>Med-Smart | Patient Dashboard</div>
    <div>
        <a href="index.php">Home</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h2>My Information</h2>
    <?php if($update_message) echo "<p class='message'>$update_message</p>"; ?>

    <form method="POST" action="">
        <label>Name</label>
        <input type="text" name="name" value="<?= $_SESSION['patient_name']; ?>" required>

        <label>Age</label>
        <input type="number" name="age" value="<?= $_SESSION['patient_age']; ?>" required>

        <label>Gender</label>
        <select name="gender" required>
            <option value="Male" <?= ($_SESSION['patient_gender']=='Male')?'selected':''; ?>>Male</option>
            <option value="Female" <?= ($_SESSION['patient_gender']=='Female')?'selected':''; ?>>Female</option>
            <option value="Other" <?= ($_SESSION['patient_gender']=='Other')?'selected':''; ?>>Other</option>
        </select>

        <label>Contact Number</label>
        <input type="text" name="contact" value="<?= $_SESSION['patient_contact']; ?>" required>

        <label>Blood Group</label>
        <input type="text" name="blood" value="<?= $_SESSION['patient_blood']; ?>" required>

        <button type="submit" name="update_info">Update Information</button>
    </form>

    <h2>My Appointments & Reports</h2>

    <table>
        <tr>
            <th>Date</th>
            <th>Time</th>
            <th>Department</th>
            <th>Doctor</th>
            <th>Status</th>
            <th>Patient Report</th>
            <th>Doctor Report</th>
            <th>Prescription</th>
        </tr>

        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['appointment_date']) ?></td>
            <td><?= htmlspecialchars($row['appointment_time']) ?></td>
            <td><?= htmlspecialchars($row['department']) ?></td>
            <td><?= htmlspecialchars($row['doctor_name']) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>

            <td>
                <?php if(!empty($row['report_path'])): ?>
                    <a href="<?= htmlspecialchars($row['report_path']) ?>" target="_blank" class="report-link">View</a>
                <?php else: ?>
                    Not uploaded
                <?php endif; ?>
            </td>

            <td>
                <?php if(!empty($row['doctor_report_path'])): ?>
                    <a href="<?= htmlspecialchars($row['doctor_report_path']) ?>" target="_blank" class="report-link">View</a>
                <?php else: ?>
                    Pending
                <?php endif; ?>
            </td>

            <td>
                <?php if(!empty($row['prescription'])): ?>
                    <div class="prescription-box">
                        <?= nl2br(htmlspecialchars($row['prescription'])) ?>
                    </div>
                <?php else: ?>
                    No prescription
                <?php endif; ?>
            </td>

        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
