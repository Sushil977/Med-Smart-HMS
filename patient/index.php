<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" />
  <title>Med Smart</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 0; padding: 0; }

    /* Top Header */
    .top-header { background-color: #007bff; color: white; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; }
    .logo { font-size: 24px; font-weight: bold; text-decoration: none; color: white; }
    .info-box { display: flex; gap: 20px; }
    .info-item a { color: white; text-decoration: none; }

    /* Buttons */
    .btn { padding: 8px 15px; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; color: white; font-weight: bold; margin-left: 5px; }
    .book-btn { background-color: #28a745; }
    .book-btn:hover { background-color: #218838; }

    /* Dropdown */
    .dropdown { position: relative; display: inline-block; }
    .dropdown-btn { background-color: #1870ba; color: white; padding: 8px 15px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
    .dropdown-content { display: none; position: absolute; background-color: #f8f9fa; min-width: 160px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); border-radius: 6px; z-index: 1; }
    .dropdown-content a { color: #2c3e50; padding: 10px 15px; text-decoration: none; display: block; }
    .dropdown-content a:hover { background-color: #007bff; color: white; }
    .dropdown:hover .dropdown-content { display: block; }
    .dropdown:hover .dropdown-btn { background-color: #1565c0; }

    /* Menu */
    .menu { background-color: #007bff; }
    .menu ul { list-style: none; display: flex; margin: 0; padding: 0; }
    .menu ul li { position: relative; }
    .menu ul li a { display: block; padding: 15px 20px; color: white; text-decoration: none; }
    .menu ul li a:hover { background-color: #0056b3; }
    .menu ul li .dropdown-menu { display: none; position: absolute; top: 100%; left: 0; background-color: #f8f9fa; min-width: 220px; border-radius: 5px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); z-index: 1000; }
    .menu ul li:hover .dropdown-menu { display: block; }
    .menu ul li .dropdown-menu li a { color: #2c3e50; }

    /* Hero Image */
    .hero-img { 
      width: 100%; 
      height: 100vh; /* full viewport height */
      object-fit: cover; /* cover entire area without stretching */
      display: block; 
    }

    /* Optional: adjust marquee for hero image space */
    marquee {
      background-color: #f8f9fa;
      padding: 10px;
      font-weight: bold;
      color: #333;
    }
  </style>
</head>

<body>
  <div class="top-header">
    <a href="index.php" class="logo">Med-Smart</a>
    <div class="info-box">
      <div class="info-item">📞 <a href="tel:+9779800000000">+977-9800000000</a></div>
      <div class="info-item">✉️ <a href="mailto:medsmart1@gmail.com">medsmart1@gmail.com</a></div>
    </div>
    <div>
      <?php if(isset($_SESSION['patient_id'])): ?>
        <a href="dashboard.php" class="btn">My Account</a>
        <a href="logout.php" class="btn">Logout</a>
      <?php else: ?>
        <div class="dropdown">
          <button class="dropdown-btn">Login</button>
          <div class="dropdown-content">
            <a href="login.php">Patient Login</a>
            <a href="../doctor/doctor_login.php">Doctor Login</a>
            <a href="../admin/admin_login.php">Admin Login</a>
          </div>
        </div>
        <div class="dropdown">
          <button class="dropdown-btn" style="background-color:#28a745;">Signup</button>
          <div class="dropdown-content">
            <a href="signup.php">Patient Signup</a>
          </div>
        </div>
      <?php endif; ?>
      <a href="appointment_doctor_list.html" class="btn book-btn">Book Appointment</a>
    </div>
  </div>

  <marquee>Your Trusted Online Hospital Ticket Booking System — Smart Care. Anytime. Anywhere.</marquee>

  <div class="menu">
    <ul>
      <li><a href="index.php">Home</a></li>
      <li><a href="about.html">About</a></li>
      <li><a href="doctor.php">Doctors</a></li>
      <li class="dropdown">
        <a href="#">Departments ▼</a>
        <ul class="dropdown-menu">
          <li><a href="doctor.php?dept=Cardiology">❤️ Cardiology</a></li>
          <li><a href="doctor.php?dept=Pulmonology">🫁 Pulmonology</a></li>
          <li><a href="doctor.php?dept=Infectious Diseases">🧬 Infectious Diseases</a></li>
          <li><a href="doctor.php?dept=ENT">👂 ENT</a></li>
          <li><a href="doctor.php?dept=Neurology">🧠 Neurology</a></li>
          <li><a href="doctor.php?dept=Oncology">🦠 Oncology</a></li>
          <li><a href="doctor.php?dept=General Checkup">🩺 General Checkup</a></li>
          <li><a href="doctor.php?dept=Orthopedics">🦴 Orthopedics</a></li>
          <li><a href="doctor.php">All Doctors</a></li>
        </ul>
      </li>
      <li><a href="contact.html">Contact</a></li>
    </ul>
  </div>

  <div>
    <img src="image/home.jpg" alt="Hospital" class="hero-img">
  </div>
</body>
</html>
