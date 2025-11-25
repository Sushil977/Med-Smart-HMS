<?php 
session_start();

// --- AUTH CHECK ---
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// --- DATABASE ---
$conn = new mysqli("localhost", "root", "", "medsmart");
if ($conn->connect_error) die("DB Error: " . $conn->connect_error);

// Directory for doctor images
$target_dir = "doctorsimages/";

// ------------------------------
// ADD DOCTOR
// ------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_doctor'])) {

    $name = $_POST['name'];
    $spec = $_POST['specialization'];
    $contact = $_POST['contact'];
    $available = $_POST['available'];
    $username = $_POST['username'];
    $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $image_path = "";

    // IMAGE UPLOAD
    if (!empty($_FILES['image']['name'])) {
        if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);

        $image_name = uniqid() . "_" . basename($_FILES['image']['name']);
        $image_path = $target_dir . $image_name;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
            die("Error uploading image.");
        }
    }

    $stmt = $conn->prepare("
        INSERT INTO doctors 
        (name, specialization, contact_number, image_path, available, username, password_hash, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("sssssss", $name, $spec, $contact, $image_path, $available, $username, $password_hash);
    $stmt->execute();
    $stmt->close();

    header("Location: doctors.php");
    exit;
}

// ------------------------------
// DELETE DOCTOR
// ------------------------------
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $res = $conn->query("SELECT image_path FROM doctors WHERE id=$id");
    if ($res && $row = $res->fetch_assoc()) {
        if (!empty($row['image_path']) && file_exists($row['image_path'])) unlink($row['image_path']);
    }

    $conn->query("DELETE FROM doctors WHERE id=$id");

    header("Location: doctors.php");
    exit;
}

// ------------------------------
// UPDATE DOCTOR
// ------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_doctor'])) {

    $id = intval($_POST['doctor_id']);
    $name = $_POST['name'];
    $spec = $_POST['specialization'];
    $contact = $_POST['contact'];
    $available = $_POST['available'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $image_path = $_POST['current_image'];

    $password_sql = "";
    if (!empty($password)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $password_sql = ", password_hash='$hash'";
    }

    // IMAGE UPDATE
    if (!empty($_FILES['image']['name'])) {
        if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);

        if (!empty($image_path) && file_exists($image_path))
            unlink($image_path);

        $image_name = uniqid() . "_" . basename($_FILES['image']['name']);
        $image_path = $target_dir . $image_name;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
            die("Error uploading new image.");
        }
    }

    $sql = "UPDATE doctors SET 
        name=?, specialization=?, contact_number=?, available=?, image_path=?, username=? 
        $password_sql 
        WHERE id=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $name, $spec, $contact, $available, $image_path, $username, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: doctors.php");
    exit;
}

// ------------------------------
// FETCH DOCTORS
// ------------------------------
$result = $conn->query("SELECT * FROM doctors ORDER BY name ASC");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Doctors - MedSmart</title>

<style>
/* --- same CSS you already had --- */
body { margin: 0; font-family: 'Segoe UI', sans-serif; background: #f7f9fb; }
.container { display: flex; }
.sidebar { width: 220px; background-color: #007bff; color: white; height: 100vh; padding-top: 30px; position: fixed; }
.sidebar h2 { text-align: center; }
.sidebar a { display: block; padding: 15px 20px; color: white; text-decoration: none; }
.sidebar a:hover { background: #0056b3; }
.main-content { margin-left: 220px; padding: 40px; width: calc(100% - 220px); }
table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; }
th, td { padding: 12px; border-bottom: 1px solid #eee; }
th { background: #007bff; color: white; }
img { width: 60px; border-radius: 6px; }
form { margin-top: 30px; background: white; padding: 25px; border-radius: 10px; }
label { font-weight: bold; }
input, select { width: 100%; padding: 10px; margin-bottom: 15px; }
input[type="submit"] { background: green; color: white; cursor: pointer; }
.delete-link { color: red; }
.edit-link { color: blue; }
</style>
</head>

<body>

<div class="container">

<div class="sidebar">
    <h2>MedSmart</h2>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="doctors.php">Doctors</a>
    <a href="patients.php">Patients</a>
    <a href="admin_logout.php">Logout</a>
</div>

<div class="main-content">
    <h1>Doctors List</h1>

    <table>
        <tr>
            <th>ID</th><th>Image</th><th>Name</th><th>Specialization</th><th>Contact</th>
            <th>Available</th><th>Username</th><th>Action</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['image_path'] ? "<img src='{$row['image_path']}'>" : "N/A" ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['specialization']) ?></td>
            <td><?= htmlspecialchars($row['contact_number']) ?></td>
            <td><?= $row['available'] ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td>
                <a class="edit-link" href="?edit=<?= $row['id'] ?>">Edit</a>
                <a class="delete-link" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete doctor?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>

    </table>

    <h2><?= isset($_GET['edit']) ? "Edit Doctor" : "Add Doctor" ?></h2>

<?php if (isset($_GET['edit'])): ?>
<?php
    $edit_id = intval($_GET['edit']);
    $edit = $conn->query("SELECT * FROM doctors WHERE id=$edit_id")->fetch_assoc();
?>
<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="doctor_id" value="<?= $edit['id'] ?>">

    <label>Name</label>
    <input type="text" name="name" value="<?= htmlspecialchars($edit['name']) ?>" required>

    <label>Specialization</label>
    <input type="text" name="specialization" value="<?= htmlspecialchars($edit['specialization']) ?>" required>

    <label>Contact</label>
    <input type="text" name="contact" value="<?= htmlspecialchars($edit['contact_number']) ?>">

    <label>Username</label>
    <input type="text" name="username" value="<?= htmlspecialchars($edit['username']) ?>" required>

    <label>Password (leave blank to keep same)</label>
    <input type="password" name="password">

    <input type="hidden" name="current_image" value="<?= $edit['image_path'] ?>">

    <label>Image</label>
    <input type="file" name="image">

    <label>Availability</label>
    <select name="available">
        <option value="Yes" <?= $edit['available']=="Yes"?"selected":"" ?>>Yes</option>
        <option value="No" <?= $edit['available']=="No"?"selected":"" ?>>No</option>
    </select>

    <input type="submit" name="edit_doctor" value="Update Doctor">
</form>

<?php else: ?>

<form method="POST" enctype="multipart/form-data">

    <label>Name</label>
    <input type="text" name="name" required>

    <label>Specialization</label>
    <select name="specialization" required>
        <option value="">-- Select --</option>
        <option value="Cardiology">Cardiology</option>
        <option value="Pulmonology">Pulmonology</option>
        <option value="ENT">ENT</option>
        <option value="Neurology">Neurology</option>
        <option value="Oncology">Oncology</option>
        <option value="General Checkup">General Checkup</option>
        <option value="Orthopedics">Orthopedics</option>
    </select>

    <label>Contact</label>
    <input type="text" name="contact">

    <label>Username</label>
    <input type="text" name="username" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <label>Image</label>
    <input type="file" name="image">

    <label>Availability</label>
    <select name="available">
        <option value="Yes">Yes</option>
        <option value="No">No</option>
    </select>

    <input type="submit" name="add_doctor" value="Add Doctor">
</form>

<?php endif; ?>

</div>
</div>

</body>
</html>

<?php $conn->close(); ?>
