<?php
session_start();
include("../connect.php");

$org_email = $_POST['OrgEmail'];
$org_pass1 = $_POST['OrgPass1'];

$sql = "SELECT org_id, org_name, org_email FROM organization_register WHERE org_email = ? AND org_password = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $org_email, $org_pass1);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $org = $result->fetch_assoc();
    $_SESSION['org_id'] = $org['org_id'];
    $_SESSION['org_name'] = $org['org_name'];
    $_SESSION['org_email'] = $org['org_email'];
    echo "<script>alert('Login Successful!'); window.location='dashboard.php';</script>";
} else {
    echo "<script>alert('Invalid Email or Password!'); window.location='login.php';</script>";
}
$stmt->close();
$conn->close();
?>