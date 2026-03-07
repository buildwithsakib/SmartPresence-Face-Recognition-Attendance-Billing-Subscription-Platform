<?php
session_start();
include("connect.php");

if (!isset($_SESSION['reg_data']) || !isset($_SESSION['OTP'])) {
    echo "<script>alert('Invalid request. Please start registration again.'); window.location.href='register.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $entered_otp = $_POST['otp1'] . $_POST['otp2'] . $_POST['otp3'] . 
                   $_POST['otp4'] . $_POST['otp5'] . $_POST['otp6'];

    if (hash_equals((string)$_SESSION['OTP'], (string)$entered_otp)) {

        $org_name = $_SESSION['reg_data']['org_name'];
        $org_email = $_SESSION['reg_data']['org_email'];
        $org_pass1 = $_SESSION['reg_data']['org_pass1']; // already hashed
        $mobile = $_SESSION['reg_data']['mobile'];

        $stmt = $conn->prepare("INSERT INTO organization_register 
            (org_name, org_email, org_password, mobile_number, created_at) 
            VALUES (?, ?, ?, ?, NOW())");

        $stmt->bind_param("ssss", $org_name, $org_email, $org_pass1, $mobile);

        if ($stmt->execute()) {

            unset($_SESSION['reg_data']);
            unset($_SESSION['OTP']);
            unset($_SESSION['mobile']);

            echo "<script>alert('Registration successful! You can now login.'); window.location.href='login.html';</script>";
            exit();

        } else {
            error_log("Database insertion failed: " . $stmt->error);
            echo "<script>alert('Registration failed. Please try again.'); window.location.href='register.php';</script>";
        }

        $stmt->close();

    } else {
        echo "<script>alert('Invalid OTP. Please try again.'); window.history.back();</script>";
    }
}
?>