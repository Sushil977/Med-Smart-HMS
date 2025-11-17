<?php
session_start(); // Start session to check if patient is logged in

$conn = new mysqli("localhost", "root", "", "medsmart");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Filter by department if provided
$dept_filter = '';
if(isset($_GET['dept']) && !empty($_GET['dept'])) {
    $dept = $conn->real_escape_string($_GET['dept']);
    $dept_filter = " AND specialization='$dept'";
}

$result = $conn->query("SELECT * FROM doctors WHERE available='Yes' $dept_filter ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Doctors - MedSmart</title>
<style>
body { font-family: Arial, sans-serif; margin:0; padding:0; }
.top-header { background-color: #f8f9fa; padding: 15px 0; border-bottom: 1px solid #ddd; }
.container { max-width: 1200px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; padding: 0 20px; }
.info-box { display: flex; gap: 20px; flex-wrap: wrap; }
.info-item { background-color: #f0f4f8; padding: 10px 18px; border-radius: 8px; font-size: 15px; font-weight: 500; color: #333; box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
.info-item a { color: #2a2424; text-decoration: none; margin-left: 6px; font-weight: 600; transition: color 0.3s ease; }
.info-item a:hover { color: #000000; }
.logo { display: flex; align-items: center; }
.circle-letter { display: inline-block; width: 40px; height: 40px; background-color: #007bff; color: white; border-radius: 50%; text-align: center; line-height: 40px; font-weight: bold; }
.logo-text { font-size: 24px; font-weight: bold; color: #2a56bb; }
.book-btn { background-color: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; text-decoration: none; display: inline-block; transition: background-color 0.3s ease; }
.book-btn:hover { background-color: #218838; }
.login { background-color: rgb(24,105,186); color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; transition: background-color 0.3s ease; }
.login:hover { background-color: rgb(7,125,242); }
.doctorlist { display: flex; flex-wrap: wrap; gap: 30px; margin: 20px; }
.doctor-card { background-color: #fff; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 20px; width: 220px; text-align: center; transition: transform 0.3s ease, box-shadow 0.3s ease; }
.doctor-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }
.doctor-card img { width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 2px solid #007bff; margin-bottom: 10px; }
.doctor-card .name { font-weight: bold; margin-bottom: 5px; color: #2a2a2a; }
.doctor-card .details { font-size: 14px; color: #555; line-height: 1.4; }

/* Menu Styles */
.menu { background-color: #007bff; padding: 0; }
.menu ul { list-style: none; margin: 0; padding: 0; display: flex; }
.menu ul li { position: relative; }
.menu ul li a { display: block; padding: 15px 20px; color: white; text-decoration: none; transition: 0.3s; }
.menu ul li a:hover { background-color: #0056b3; }
.menu ul li .dropdown-menu { display: none; position: absolute; top: 100%; left: 0; background-color: #f8f9fa; min-width: 220px; border-radius: 5px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); z-index: 1000; }
.menu ul li .dropdown-menu li a { color: #2c3e50; padding: 10px 15px; display: flex; align-items: center; }
.menu ul li:hover .dropdown-menu { display: block; }
.menu ul li .arrow { margin-left: 5px; font-size: 12px; }
.dropdown-menu li a span { font-size: 20px; margin-right: 10px; }
</style>
</head>
<body>

<div class="top-header">
  <div class="container">
    <a href="index.php" class="logo">
      <i class="circle-letter">Med-</i>
      <div class="logo-text">Smart</div>
    </a>
    <div class="info-box">
      <div class="info-item">📞 Emergency:<a href="tel:+9779800000000">+977-9800000000</a></div>
      <div class="info-item">✉️ Email:<a href="mailto:medsmart1@gmail.com">medsmart1@gmail.com</a></div>
    </div>
    <div>
      <?php if(isset($_SESSION['patient_id'])): ?>
        <a href="dashboard.php"><button class="login">My Account</button></a>
        <a href="logout.php"><button class="login">Logout</button></a>
      <?php else: ?>
        <a href="login.php"><button class="login">Login & Signup</button></a>
      <?php endif; ?>
    </div>
  </div>
</div>

<marquee>Your Trusted Online Hospital Management System — Smart Care. Anytime. Anywhere.</marquee>

<!-- Menu -->
<div class="menu">
  <ul>
    <li><a href="index.php">Home</a></li>
    <li><a href="about.html">About</a></li>
    <li><a href="doctor.php">Doctors</a></li>
    <li class="dropdown">
      <a href="#">Departments <span class="arrow">▼</span></a>
      <ul class="dropdown-menu">
        <li><a href="doctor.php?dept=Cardiology"><span style="color:#ff4d4d;">❤️</span> Cardiology</a></li>
        <li><a href="doctor.php?dept=Pulmonology"><span style="color:#4CAF50;">🫁</span> Pulmonology</a></li>
        <li><a href="doctor.php?dept=Infectious Diseases"><span style="color:#f39c12;">🧬</span> Infectious Diseases</a></li>
        <li><a href="doctor.php?dept=ENT"><span style="color:#9b59b6;">👂</span> ENT</a></li>
        <li><a href="doctor.php?dept=Neurology"><span style="color:#3498db;">🧠</span> Neurology</a></li>
        <li><a href="doctor.php?dept=Oncology"><span style="color:#e74c3c;">🦠</span> Oncology</a></li>
        <li><a href="doctor.php?dept=General Checkup"><span style="color:#16a085;">🩺</span> General Checkup</a></li>
        <li><a href="doctor.php?dept=Orthopedics"><span style="color:#e67e22;">🦴</span> Orthopedics</a></li>
        <li><a href="doctor.php">All Doctors</a></li>
      </ul>
    </li>
    <li><a href="contact.html">Contact</a></li>
  </ul>
</div>

<!-- Doctors List -->
<div class="doctorlist">
<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dbImagePath = $row['image_path'];
        $imgPath = (!empty($dbImagePath) && file_exists('../admin/' . $dbImagePath)) ? '../admin/' . $dbImagePath : '../image/doctors/default.png';
        echo '<div class="doctor-card">';
        echo '<img src="'.htmlspecialchars($imgPath).'" alt="Doctor">';
        echo '<div class="name">Dr. '.htmlspecialchars($row['name']).'</div>';
        echo '<div class="details">'.htmlspecialchars($row['specialization']).'<br>Experience: 5+ Years<br>Patients: 300+</div>';

        // Only allow logged-in patients to book
        if(isset($_SESSION['patient_id'])) {
            echo '<div><a href="appointment_form.php?doctor_id='.$row['id'].'" class="book-btn">Book Appointment</a></div>';
        } else {
            echo '<div><a href="signup.php" class="book-btn">Book Appointment</a></div>';
        }

        echo '</div>';
    }
} else {
    echo '<p style="margin:20px;">No doctors available for this department.</p>';
}
$conn->close();
?>
</div>

</body>
</html>
