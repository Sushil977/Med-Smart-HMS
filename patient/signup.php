<?php
$conn = new mysqli("localhost", "root", "", "medsmart");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $contact = $conn->real_escape_string($_POST['contact']);
    $age = intval($_POST['age']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $blood = $conn->real_escape_string($_POST['blood_group']);

    $sql = "INSERT INTO patients (name, email, password_hash, contact_number, age, gender, blood_group, created_at) VALUES ('$name','$email','$password','$contact',$age,'$gender','$blood', NOW())";
    if ($conn->query($sql)) {
    // Redirect to login page
    header("Location: login.php");
    exit();
} else {
    $message = "Error: " . $conn->error;
}

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Patient Signup</title>
<style>
body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f9ff; margin:0; padding:0; }
.form-container { max-width: 400px; margin: 50px auto; padding: 30px; background: #fff; border-radius: 12px; box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
.form-container h2 { text-align:center; color:#2c3e50; margin-bottom:25px; }
.form-container input, .form-container select { width:100%; padding:10px; margin-bottom:15px; border-radius:6px; border:1px solid #ccc; }
.form-container button { width:100%; padding:12px; border:none; border-radius:8px; background:#1976d2; color:white; font-weight:bold; cursor:pointer; }
.form-container button:hover { background:#1565c0; }
p { color:red; text-align:center; }
.switch { text-align:center; margin-top:15px; font-size:14px; }
.switch a { color:#1976d2; text-decoration:none; font-weight:bold; }
.switch a:hover { text-decoration:underline; }
</style>
</head>
<body>
<div class="form-container">
  <h2>Patient Signup</h2>
  <?php if($message) echo "<p>$message</p>"; ?>
  <form method="POST">
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="" name="contact" placeholder="Contact Number" required>
    <input type="number" name="age" placeholder="Age" required>
    <select name="gender" required>
        <option value="">Select Gender</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
        <option value="Other">Other</option>
    </select>
    <select name="blood_group" required>
        <option value="">Select Blood Group</option>
        <option value="A+">A+</option>
        <option value="A-">A-</option>
        <option value="B+">B+</option>
        <option value="B-">B-</option>
        <option value="AB+">AB+</option>
        <option value="AB-">AB-</option>
        <option value="O+">O+</option>
        <option value="O-">O+</option>
    </select>
    <button type="submit"> <a href="login.php"></a>Signup</button>
  </form>
  <div class="switch">Already have an account? <a href="login.php">Login</a></div>
</div>
</body>
</html>
