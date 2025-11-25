<?php
session_start();

// Appointment details
$doctor_id = isset($_GET['doctor_id']) ? intval($_GET['doctor_id']) : 0;
$doctor_name = isset($_GET['doctor_name']) ? htmlspecialchars($_GET['doctor_name']) : '';
$department = isset($_GET['department']) ? htmlspecialchars($_GET['department']) : '';

// Payment details
$amount = 100;        // Base amount
$tax_amount = 10;     // Tax
$total_amount = $amount + $tax_amount;
$transaction_uuid = 'appt_' . time() . '-' . bin2hex(random_bytes(4)); // Unique UUID
$product_code = "EPAYTEST";

// eSewa URLs
$success_url = "https://developer.esewa.com.np/success";
$failure_url = "https://developer.esewa.com.np/failure";

// eSewa sandbox secret key
$secret_key = "8gBm/:&EnhH.1/q"; // Provided for UAT

// Create signature using HMAC SHA-256 and Base64
$fields = [
    'total_amount' => $total_amount,
    'transaction_uuid' => $transaction_uuid,
    'product_code' => $product_code
];

// Concatenate fields in the same order
$data_string = "total_amount={$fields['total_amount']},transaction_uuid={$fields['transaction_uuid']},product_code={$fields['product_code']}";

// Generate HMAC SHA256 signature and encode in Base64
$signature = base64_encode(hash_hmac('sha256', $data_string, $secret_key, true));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Book Appointment</title>
    <style>
        body { font-family: Arial, sans-serif; background: #eef1f5; margin: 0; padding: 0; }
        .form-container { width: 420px; margin: 50px auto; background: white; padding: 25px; border-radius: 12px; box-shadow: 0px 4px 12px rgba(0,0,0,0.15); }
        h2 { text-align: center; margin-bottom: 25px; color: #333; }
        label { font-weight: bold; color: #333; }
        input, select { width: 100%; padding: 12px; margin-top: 5px; margin-bottom: 18px; border: 1px solid #c7c7c7; border-radius: 8px; font-size: 15px; }
        p { background: #f8f9fa; padding: 10px; border-radius: 6px; font-size: 14px; color: #444; margin-bottom: 15px; }
        button { width: 100%; padding: 14px; border: none; color: white; border-radius: 8px; font-size: 17px; cursor: pointer; font-weight: bold; transition: 0.3s; }
        button:hover { opacity: 0.9; }
        .book-btn { background: #007bff; }
        .payment-btn { background: #28a745; }
        @media (max-width: 500px) { .form-container { width: 90%; } }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Book Appointment</h2>

    <form method="POST" action="appointment.php" enctype="multipart/form-data" id="appointmentForm">

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

        <!-- Buttons -->
        <div style="display: flex; gap: 10px; justify-content: space-between; margin-top: 10px;">
            <button type="submit" name="book" class="book-btn" style="flex:1;">Book Appointment</button>
            <button type="button" class="payment-btn" style="flex:1;" onclick="document.getElementById('esewaForm').submit();">Payment</button>
        </div>
    </form>

    <!-- Hidden eSewa Form -->
    <form action="https://rc-epay.esewa.com.np/api/epay/main/v2/form" method="POST" id="esewaForm" style="display:none;">
        <input type="text" name="amount" value="<?php echo $amount; ?>" required>
        <input type="text" name="tax_amount" value="<?php echo $tax_amount; ?>" required>
        <input type="text" name="total_amount" value="<?php echo $total_amount; ?>" required>
        <input type="text" name="transaction_uuid" value="<?php echo $transaction_uuid; ?>" required>
        <input type="text" name="product_code" value="<?php echo $product_code; ?>" required>
        <input type="text" name="product_service_charge" value="0" required>
        <input type="text" name="product_delivery_charge" value="0" required>
        <input type="text" name="success_url" value="<?php echo $success_url; ?>" required>
        <input type="text" name="failure_url" value="<?php echo $failure_url; ?>" required>
        <input type="text" name="signed_field_names" value="total_amount,transaction_uuid,product_code" required>
        <input type="text" name="signature" value="<?php echo $signature; ?>" required>
    </form>
</div>

</body>
</html>
