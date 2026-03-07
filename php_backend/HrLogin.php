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

// Fetch user by email
$sql = "SELECT * FROM organization_register WHERE org_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $org_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $row = $result->fetch_assoc();

    // Verify hashed password
    if (password_verify($org_pass1, $row['org_password'])) {

        $_SESSION['OrgEmail'] = $org_email;

        echo '<script>
            alert("Hr Login Successful!");
            window.location = "employeeForm.html";
        </script>';

    } else {

        echo '<script>
            alert("Invalid Email or Password!");
            window.location = "HrLogin.html";
        </script>';

    }

} else {

    echo '<script>
        alert("Invalid Email or Password!");
        window.location = "HrLogin.html";
    </script>';

}

$stmt->close();
$conn->close();
?>