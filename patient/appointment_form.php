<?php
session_start();

$doctor_id = isset($_GET['doctor_id']) ? intval($_GET['doctor_id']) : 0;
$doctor_name = isset($_GET['doctor_name']) ? htmlspecialchars($_GET['doctor_name']) : '';
$department = isset($_GET['department']) ? htmlspecialchars($_GET['department']) : '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Book Appointment</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef1f5;
            margin: 0;
            padding: 0;
        }

        .form-container {
            width: 420px;
            margin: 50px auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0px 4px 12px rgba(0,0,0,0.15);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        label {
            font-weight: bold;
            color: #333;
        }

        input, select {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            margin-bottom: 18px;
            border: 1px solid #c7c7c7;
            border-radius: 8px;
            font-size: 15px;
        }

        p {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 6px;
            font-size: 14px;
            color: #444;
            margin-bottom: 15px;
        }

        button {
            width: 100%;
            padding: 14px;
            border: none;
            background: #007bff;
            color: white;
            border-radius: 8px;
            font-size: 17px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }

        button:hover {
            background: #0056b3;
        }

        @media (max-width: 500px) {
            .form-container {
                width: 90%;
            }
        }
    </style>

</head>
<body>

<div class="form-container">
    <h2>Book Appointment</h2>

    <form method="POST" action="appointment.php" enctype="multipart/form-data">

        <!-- Hidden Inputs -->
        <input type="hidden" name="doctor_id" value="<?php echo $doctor_id; ?>">
        <input type="hidden" name="doctor_name" value="<?php echo $doctor_name; ?>">
        <input type="hidden" name="department" value="<?php echo $department; ?>">

        <!-- Doctor Info -->
        <p><strong>Doctor:</strong> <?php echo $doctor_name; ?></p>
        <p><strong>Department:</strong> <?php echo $department; ?></p>

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
        <input type="date" name="appointment_date" required>

        <label>Appointment Time</label>
        <input type="time" name="appointment_time" required>

        <label>Upload Report (optional)</label>
        <input type="file" name="patient_image">

        <button type="submit">Book Appointment</button>
    </form>
</div>

</body>
</html>
