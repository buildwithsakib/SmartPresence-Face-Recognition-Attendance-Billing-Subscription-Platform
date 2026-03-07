<?php
session_start();
include("connect.php"); // Connects to your MySQL database

// Get form data
$org_email = $_POST['OrgEmail'];
$org_pass1 = $_POST['OrgPass1'];

// Check for connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user by email only
$sql = "SELECT org_id, org_email, org_name, org_password FROM organization_register WHERE org_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $org_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $org_data = $result->fetch_assoc();

    // VERIFY HASHED PASSWORD
    if (password_verify($org_pass1, $org_data['org_password'])) {

        // Store organization data in session
        $_SESSION['org_id'] = $org_data['org_id'];
        $_SESSION['OrgEmail'] = $org_data['org_email'];
        $_SESSION['org_name'] = $org_data['org_name'];

        echo '<script>
            alert("Login Successful!");
            window.location = "Dashboard.php";
        </script>';

    } else {

        echo '<script>
            alert("Invalid Email or Password!");
            window.location = "login.html";
        </script>';

    }

} else {

    echo '<script>
        alert("Invalid Email or Password!");
        window.location = "login.html";
    </script>';

}

$stmt->close();
$conn->close();
?>