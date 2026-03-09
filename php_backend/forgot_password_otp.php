<?php
session_start();
include("connect.php");

// If no OTP session exists, redirect back to forgot password page
if (!isset($_SESSION['forgot_otp']) || !isset($_SESSION['forgot_org_id'])) {
    header("Location: forgot_password.php");
    exit();
}

// Handle OTP resend (only via GET)
if (isset($_GET['resend'])) {
    // Generate new OTP
    $otp = rand(100000, 999999);
    $_SESSION['forgot_otp'] = $otp;

    // Send OTP via SMS
    $mobile = $_SESSION['forgot_mobile'];
    $API = "YOUR_SMS_API";
    $URL = "https://sms.renflair.in/V1.php?API=$API&PHONE=$mobile&OTP=$otp";
    file_get_contents($URL); // or use cURL

    $resend_success = "A new OTP has been sent to your mobile.";
    // No redirect, just show the form again with success message
}

// Handle OTP verification (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_otp = trim($_POST['otp']);

    // Compare as strings to avoid type issues
    if ((string)$entered_otp === (string)$_SESSION['forgot_otp']) {
        // OTP correct – clear OTP from session and proceed to reset
        unset($_SESSION['forgot_otp']);
        header("Location: forgot_password_reset.php");
        exit();
    } else {
        $error = "Invalid OTP. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP | SmartPresence</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Same style as before – keep consistent */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        :root { --primary: #2c3e50; --secondary: #3498db; --accent: #1abc9c; --light: #ecf0f1; --dark: #2c3e50; --success: #2ecc71; --warning: #f39c12; }
        body { background: linear-gradient(135deg, var(--primary), #1a2530); color: var(--light); min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; position: relative; overflow: hidden; }
        .background-elements { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; overflow: hidden; }
        .circle, .square, .triangle { position: absolute; opacity: 0.05; animation: float 15s infinite ease-in-out; }
        .circle { border-radius: 50%; background: white; }
        .square { background: var(--secondary); }
        .triangle { width: 0; height: 0; border-style: solid; border-color: transparent transparent var(--accent) transparent; }
        @keyframes float { 0%,100%{ transform: translate(0,0); } 25%{ transform: translate(10px,15px); } 50%{ transform: translate(-5px,20px); } 75%{ transform: translate(15px,-10px); } }
        .container { background: rgba(255,255,255,0.08); border-radius: 15px; width: 100%; max-width: 400px; padding: 2.5rem; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 10px 30px rgba(0,0,0,0.3); position: relative; animation: fadeInUp 0.8s ease-out; }
        .container::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 5px; background: linear-gradient(to right, var(--accent), var(--secondary)); }
        h2 { text-align: center; font-size: 2rem; margin-bottom: 1rem; background: linear-gradient(to right, var(--accent), var(--secondary)); -webkit-background-clip: text; background-clip: text; color: transparent; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; color: var(--light); font-weight: 500; }
        .form-control { width: 100%; padding: 14px; background: rgba(255,255,255,0.1); border: 2px solid rgba(255,255,255,0.15); border-radius: 8px; font-size: 16px; color: var(--light); transition: all 0.3s ease; text-align: center; letter-spacing: 5px; }
        .form-control:focus { outline: none; border-color: var(--accent); background: rgba(255,255,255,0.15); box-shadow: 0 0 0 3px rgba(26,188,156,0.2); }
        .btn { background: linear-gradient(to right, var(--accent), var(--secondary)); color: white; border: none; padding: 14px; font-size: 1.1rem; font-weight: 600; border-radius: 8px; cursor: pointer; transition: 0.3s; width: 100%; letter-spacing: 0.5px; margin-top: 10px; }
        .btn:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
        .error { background: rgba(231,76,60,0.2); border: 1px solid #e74c3c; padding: 10px; border-radius: 5px; margin-bottom: 1rem; color: #e74c3c; }
        .success { background: rgba(46,204,113,0.2); border: 1px solid #2ecc71; padding: 10px; border-radius: 5px; margin-bottom: 1rem; color: #2ecc71; }
        .resend-link { text-align: center; margin-top: 1.5rem; }
        .resend-link a { color: var(--accent); text-decoration: none; font-weight: 600; }
        .resend-link a:hover { text-decoration: underline; }
        footer { text-align: center; padding: 1rem; color: #95a5a6; position: absolute; bottom: 0; width: 100%; }
        @keyframes fadeInUp { from { opacity:0; transform: translateY(30px); } to { opacity:1; transform: translateY(0); } }
    </style>
</head>
<body>
    <div class="background-elements">
        <div class="circle" style="width:200px;height:200px;top:70%;left:10%;"></div>
        <div class="square" style="width:150px;height:150px;top:30%;left:80%;"></div>
        <div class="triangle" style="border-width:0 70px 120px 70px;top:60%;left:70%;"></div>
    </div>

    <div class="container">
        <h2><i class="fas fa-shield-alt"></i> Verify OTP</h2>

        <?php if (isset($error)): ?>
            <div class="error"><i class="fas fa-times-circle"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($resend_success)): ?>
            <div class="success"><i class="fas fa-check-circle"></i> <?php echo $resend_success; ?></div>
        <?php endif; ?>

        <p style="text-align:center; margin-bottom:20px;">
            OTP sent to <strong><?php echo substr($_SESSION['forgot_mobile'], 0, 4) . '****' . substr($_SESSION['forgot_mobile'], -2); ?></strong>
        </p>

        <form method="post">
            <div class="form-group">
                <label>Enter 6-digit OTP</label>
                <input type="text" name="otp" class="form-control" placeholder="------" maxlength="6" required autofocus>
            </div>
            <button type="submit" class="btn"><i class="fas fa-check"></i> Verify OTP</button>
        </form>

        <div class="resend-link">
            <a href="?resend=1"><i class="fas fa-redo-alt"></i> Resend OTP</a> | 
            <a href="forgot_password.php"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>

    <footer>
        <p>SmartPresence | Forgot Password</p>
    </footer>
</body>
</html>