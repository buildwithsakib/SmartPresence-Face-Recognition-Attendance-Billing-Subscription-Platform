<?php
session_start();
include("connect.php");

// Check if organization is logged in
if (!isset($_SESSION['org_id'])) {
    echo "<script>alert('Please login as an organization first!'); window.location.href='org_login.php';</script>";
    exit();
}

$org_id = $_SESSION['org_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $empname    = $_POST['empname'];
    $email      = $_POST['email'];
    $password   = $_POST['password'];
    $repassword = $_POST['repassword'];
    $dob        = $_POST['dob'];
    $mobile     = $_POST['mobile'];
    $category   = $_POST['category'];

    // Check for duplicate email or mobile in employees table for this organization
    $check_email = mysqli_query($conn, "SELECT * FROM employees WHERE email = '$email' AND org_id = '$org_id'");
    $check_mobile = mysqli_query($conn, "SELECT * FROM employees WHERE mobile = '$mobile' AND org_id = '$org_id'");

    // Also check in employees_master for global uniqueness
    $check_master_email = mysqli_query($conn, "SELECT * FROM employees_master WHERE email = '$email'");
    $check_master_mobile = mysqli_query($conn, "SELECT * FROM employees_master WHERE mobile = '$mobile'");

    if (mysqli_num_rows($check_email) > 0) {
        echo "<script>alert('Email already registered in your organization!'); window.history.back();</script>";
    } elseif (mysqli_num_rows($check_mobile) > 0) {
        echo "<script>alert('Mobile number already registered in your organization!'); window.history.back();</script>";
    } elseif (mysqli_num_rows($check_master_email) > 0) {
        echo "<script>alert('Email already registered in another organization!'); window.history.back();</script>";
    } elseif (mysqli_num_rows($check_master_mobile) > 0) {
        echo "<script>alert('Mobile number already registered in another organization!'); window.history.back();</script>";
    } elseif ($password !== $repassword) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
    } else {

        $dobDate = new DateTime($dob);
        $today = new DateTime();
        $age = $today->diff($dobDate)->y;

        if ($age < 18) {
            echo "<script>alert('Employee must be at least 18 years old!'); window.history.back();</script>";
            exit();
        }

        // PASSWORD HASH
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Store data in session instead of database
        $_SESSION['pending_employee'] = [
            'org_id' => $org_id,
            'empname' => $empname,
            'email' => $email,
            'password' => $hashed_password,
            'dob' => $dob,
            'mobile' => $mobile,
            'category' => $category
        ];

        // Generate and send OTP
        $otp = rand(100000, 999999);
        $_SESSION['OTP'] = $otp;
        $_SESSION['mobile'] = $mobile;

        $API = "YOUR_API_KEY"; 
        $URL = "https://sms.renflair.in/V1.php?API=$API&PHONE=$mobile&OTP=$otp";

        $curl = curl_init($URL);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);

        echo "<script>alert('OTP sent to your mobile number.'); window.location.href='Employee-otp.php';</script>";
        exit();
    }
}
?>