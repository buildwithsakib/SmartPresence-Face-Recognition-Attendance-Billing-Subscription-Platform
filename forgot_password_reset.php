<?php
session_start();
include("connect.php");

// Must have org_id in session from previous step
if (!isset($_SESSION['forgot_org_id'])) {
    header("Location: forgot_password.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($new_pass !== $confirm_pass) {
        $error = "Passwords do not match.";
    } elseif (strlen($new_pass) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Hash the new password
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
        $org_id = $_SESSION['forgot_org_id'];

        $stmt = $conn->prepare("UPDATE organization_register SET org_password = ? WHERE org_id = ?");
        $stmt->bind_param("si", $hashed, $org_id);
        if ($stmt->execute()) {
            // Clear forgot session variables
            unset($_SESSION['forgot_org_id']);
            unset($_SESSION['forgot_mobile']);
            unset($_SESSION['forgot_email']);
            echo "<script>alert('Password updated successfully! Please login with your new password.'); window.location='login.html';</script>";
            exit();
        } else {
            $error = "Something went wrong. Please try again.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | SmartPresence</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Same base style as before plus password toggle */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        :root { --primary: #2c3e50; --secondary: #3498db; --accent: #1abc9c; --light: #ecf0f1; --dark: #2c3e50; --success: #2ecc71; --warning: #f39c12; }
        body { background: linear-gradient(135deg, var(--primary), #1a2530); color: var(--light); min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; position: relative; overflow: hidden; }
        .background-elements { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; overflow: hidden; }
        .circle, .square, .triangle { position: absolute; opacity: 0.05; animation: float 15s infinite ease-in-out; }
        .circle { border-radius: 50%; background: white; }
        .square { background: var(--secondary); }
        .triangle { width: 0; height: 0; border-style: solid; border-color: transparent transparent var(--accent) transparent; }
        @keyframes float { 0%,100%{ transform: translate(0,0); } 25%{ transform: translate(10px,15px); } 50%{ transform: translate(-5px,20px); } 75%{ transform: translate(15px,-10px); } }
        .container { background: rgba(255,255,255,0.08); border-radius: 15px; width: 100%; max-width: 450px; padding: 2.5rem; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 10px 30px rgba(0,0,0,0.3); position: relative; animation: fadeInUp 0.8s ease-out; }
        .container::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 5px; background: linear-gradient(to right, var(--accent), var(--secondary)); }
        h2 { text-align: center; font-size: 2rem; margin-bottom: 1rem; background: linear-gradient(to right, var(--accent), var(--secondary)); -webkit-background-clip: text; background-clip: text; color: transparent; }
        .form-group { margin-bottom: 1.5rem; position: relative; }
        .form-group label { display: block; margin-bottom: 0.5rem; color: var(--light); font-weight: 500; }
        .password-container { position: relative; }
        .form-control { width: 100%; padding: 14px; background: rgba(255,255,255,0.1); border: 2px solid rgba(255,255,255,0.15); border-radius: 8px; font-size: 16px; color: var(--light); transition: all 0.3s ease; }
        .form-control:focus { outline: none; border-color: var(--accent); background: rgba(255,255,255,0.15); box-shadow: 0 0 0 3px rgba(26,188,156,0.2); }
        .toggle-password { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #bdc3c7; cursor: pointer; font-size: 18px; }
        .toggle-password:hover { color: var(--accent); }
        .btn { background: linear-gradient(to right, var(--accent), var(--secondary)); color: white; border: none; padding: 14px; font-size: 1.1rem; font-weight: 600; border-radius: 8px; cursor: pointer; transition: 0.3s; width: 100%; letter-spacing: 0.5px; margin-top: 10px; }
        .btn:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
        .error { background: rgba(231,76,60,0.2); border: 1px solid #e74c3c; padding: 10px; border-radius: 5px; margin-bottom: 1rem; color: #e74c3c; }
        .login-link { text-align: center; margin-top: 1.5rem; }
        .login-link a { color: var(--accent); text-decoration: none; font-weight: 600; }
        footer { text-align: center; padding: 1rem; color: #95a5a6; position: absolute; bottom: 0; width: 100%; }
        @keyframes fadeInUp { from { opacity:0; transform: translateY(30px); } to { opacity:1; transform: translateY(0); } }
    </style>
</head>
<body>
    <div class="background-elements">
        <div class="circle" style="width:250px;height:250px;top:15%;left:15%;"></div>
        <div class="square" style="width:180px;height:180px;top:65%;left:75%;"></div>
        <div class="triangle" style="border-width:0 90px 150px 90px;top:30%;left:70%;"></div>
    </div>

    <div class="container">
        <h2><i class="fas fa-lock"></i> Set New Password</h2>
        <?php if (isset($error)) echo "<div class='error'><i class='fas fa-exclamation-circle'></i> $error</div>"; ?>
        <form method="post">
            <div class="form-group">
                <label>New Password</label>
                <div class="password-container">
                    <input type="password" name="new_password" id="new_password" class="form-control" placeholder="At least 6 characters" required minlength="6">
                    <span class="toggle-password" onclick="togglePassword('new_password')"><i class="fas fa-eye"></i></span>
                </div>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <div class="password-container">
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Re-enter new password" required minlength="6">
                    <span class="toggle-password" onclick="togglePassword('confirm_password')"><i class="fas fa-eye"></i></span>
                </div>
            </div>
            <button type="submit" class="btn"><i class="fas fa-sync-alt"></i> Update Password</button>
        </form>
        <div class="login-link">
            <a href="login.html"><i class="fas fa-arrow-left"></i> Back to Login</a>
        </div>
    </div>

    <footer>
        <p>SmartPresence | Reset Password</p>
    </footer>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling.querySelector('i');
            if (field.type === "password") {
                field.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>