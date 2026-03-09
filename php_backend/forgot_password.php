<?php
session_start();
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);

    // Check if organization exists with both email and mobile
    $stmt = $conn->prepare("SELECT org_id, org_name FROM organization_register WHERE org_email = ? AND mobile_number = ?");
    $stmt->bind_param("ss", $email, $mobile);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $org = $result->fetch_assoc();
        $org_id = $org['org_id'];
        $org_name = $org['org_name'];

        // Generate OTP
        $otp = rand(100000, 999999);
        $_SESSION['forgot_otp'] = $otp;
        $_SESSION['forgot_org_id'] = $org_id;
        $_SESSION['forgot_mobile'] = $mobile;
        $_SESSION['forgot_email'] = $email;

        // Send OTP via SMS
        $API = "YOUR_SMS_API"; // your API key
        $URL = "https://sms.renflair.in/V1.php?API=$API&PHONE=$mobile&OTP=$otp";
        file_get_contents($URL); // or use curl

        echo "<script>alert('OTP sent to your registered mobile number.'); window.location='forgot_password_otp.php';</script>";
        exit();
    } else {
        $error = "No organization found with that email and mobile number.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | SmartPresence</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Copy the exact same style from register.php – I'll place it in a separate CSS block, but you can reuse */
        /* For brevity, I'll include the key styles; in production you can link a common CSS file */
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
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; color: var(--light); font-weight: 500; }
        .form-control { width: 100%; padding: 14px; background: rgba(255,255,255,0.1); border: 2px solid rgba(255,255,255,0.15); border-radius: 8px; font-size: 16px; color: var(--light); transition: all 0.3s ease; }
        .form-control:focus { outline: none; border-color: var(--accent); background: rgba(255,255,255,0.15); box-shadow: 0 0 0 3px rgba(26,188,156,0.2); }
        .btn { background: linear-gradient(to right, var(--accent), var(--secondary)); color: white; border: none; padding: 14px; font-size: 1.1rem; font-weight: 600; border-radius: 8px; cursor: pointer; transition: 0.3s; width: 100%; letter-spacing: 0.5px; margin-top: 10px; position: relative; overflow: hidden; }
        .btn:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
        .btn i { margin-right: 8px; }
        .error { background: rgba(231,76,60,0.2); border: 1px solid #e74c3c; padding: 10px; border-radius: 5px; margin-bottom: 1rem; color: #e74c3c; }
        .login-link { text-align: center; margin-top: 1.5rem; }
        .login-link a { color: var(--accent); text-decoration: none; font-weight: 600; }
        .login-link a:hover { text-decoration: underline; }
        footer { text-align: center; padding: 1rem; color: #95a5a6; position: absolute; bottom: 0; width: 100%; }
        @keyframes fadeInUp { from { opacity:0; transform: translateY(30px); } to { opacity:1; transform: translateY(0); } }
    </style>
</head>
<body>
    <div class="background-elements">
        <div class="circle" style="width:300px;height:300px;top:10%;left:5%;"></div>
        <div class="square" style="width:200px;height:200px;top:20%;left:85%;"></div>
        <div class="triangle" style="border-width:0 100px 170px 100px;top:40%;left:25%;"></div>
    </div>

    <div class="container">
        <h2><i class="fas fa-key" style="margin-right:10px;"></i>Forgot Password</h2>
        <?php if (isset($error)) echo "<div class='error'><i class='fas fa-exclamation-circle'></i> $error</div>"; ?>
        <form method="post">
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Organization Email</label>
                <input type="email" name="email" class="form-control" placeholder="Enter registered email" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-mobile-alt"></i> Registered Mobile Number</label>
                <input type="text" name="mobile" class="form-control" placeholder="Enter 10-digit mobile" required>
            </div>
            <button type="submit" class="btn"><i class="fas fa-paper-plane"></i> Send OTP</button>
        </form>
        <div class="login-link">
            <a href="login.html"><i class="fas fa-arrow-left"></i> Back to Login</a>
        </div>
    </div>

    <footer>
        <p>SmartPresence: Face Recognition Attendance, Billing & Subscription</p>
    </footer>
</body>
</html>