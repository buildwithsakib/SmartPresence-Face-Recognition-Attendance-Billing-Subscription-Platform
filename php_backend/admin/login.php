<?php
session_start();
if (isset($_SESSION['org_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | SmartPresence</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        :root {
            --primary: #2c3e50;
            --secondary: #8e44ad;     /* purple */
            --accent: #9b59b6;         /* light purple */
            --light: #ecf0f1;
            --dark: #2c3e50;
        }
        body {
            background: linear-gradient(135deg, var(--primary), #1a2530);
            color: var(--light);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        .background-elements {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: -1;
            overflow: hidden;
        }
        .circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            animation: float 15s infinite ease-in-out;
        }
        .square {
            position: absolute;
            background: rgba(142,68,173,0.05);
            animation: rotate 20s infinite linear;
        }
        .triangle {
            position: absolute;
            width: 0; height: 0;
            border-style: solid;
            border-color: transparent transparent rgba(155,89,182,0.05) transparent;
            animation: float 18s infinite ease-in-out reverse;
        }
        .login-container {
            background: rgba(255,255,255,0.08);
            border-radius: 15px;
            padding: 3rem;
            width: 90%;
            max-width: 400px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            position: relative;
            overflow: hidden;
        }
        .login-container::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 5px;
            background: linear-gradient(to right, var(--accent), var(--secondary));
        }
        .logo {
            font-size: 3rem;
            color: var(--accent);
            text-align: center;
            margin-bottom: 1rem;
        }
        h2 {
            text-align: center;
            margin-bottom: 2rem;
            background: linear-gradient(to right, var(--accent), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--light);
        }
        .form-control {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 8px;
            color: var(--light);
            font-size: 1rem;
            transition: all 0.3s;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(155,89,182,0.2);
        }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(to right, var(--accent), var(--secondary));
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(155,89,182,0.4);
            position: relative;
            overflow: hidden;
        }
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(155,89,182,0.6);
        }
        .btn-login::after {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }
        .btn-login:hover::after {
            left: 100%;
        }
        .back-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        .back-link a {
            color: var(--accent);
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
        footer {
            position: absolute;
            bottom: 20px;
            width: 100%;
            text-align: center;
            color: #95a5a6;
            font-size: 0.9rem;
        }
        @keyframes float {
            0%,100%{ transform: translate(0,0); }
            25%{ transform: translate(10px,15px); }
            50%{ transform: translate(-5px,20px); }
            75%{ transform: translate(15px,-10px); }
        }
        @keyframes rotate {
            from{ transform: rotate(0deg); }
            to{ transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="background-elements">
        <div class="circle" style="width:250px;height:250px;top:10%;left:5%;"></div>
        <div class="circle" style="width:150px;height:150px;top:70%;left:80%;"></div>
        <div class="square" style="width:200px;height:200px;top:20%;left:85%;"></div>
        <div class="triangle" style="border-width:0 100px 170px 100px;top:40%;left:25%;"></div>
    </div>
    <div class="login-container">
        <div class="logo"><i class="fas fa-user-shield"></i></div>
        <h2>Admin Login</h2>
        <form action="login_process.php" method="post">
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Organization Email</label>
                <input type="email" name="OrgEmail" class="form-control" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="OrgPass1" class="form-control" required>
            </div>
            <button type="submit" class="btn-login"><i class="fas fa-sign-in-alt"></i> Login</button>
        </form>
        <div class="back-link"><a href="../start.html"><i class="fas fa-arrow-left"></i> Back to Home</a></div>
    </div>
    <footer>&copy; 2025 SmartPresence Admin</footer>
    <script>
        function createBackgroundElements() {
            const container = document.querySelector('.background-elements');
            const types = ['circle','square','triangle'];
            const colors = ['rgba(155,89,182,0.05)','rgba(142,68,173,0.05)','rgba(52,152,219,0.05)'];
            for (let i=0; i<8; i++) {
                const type = types[Math.floor(Math.random()*types.length)];
                const el = document.createElement('div');
                el.className = type;
                const size = Math.random()*150+50;
                const top = Math.random()*100;
                const left = Math.random()*100;
                const duration = Math.random()*20+10;
                if (type === 'triangle') {
                    const borderWidth = size/2;
                    el.style.borderWidth = `0 ${borderWidth}px ${size}px ${borderWidth}px`;
                    el.style.borderColor = `transparent transparent ${colors[Math.floor(Math.random()*colors.length)]} transparent`;
                } else {
                    el.style.width = `${size}px`;
                    el.style.height = `${size}px`;
                    el.style.backgroundColor = colors[Math.floor(Math.random()*colors.length)];
                }
                el.style.top = `${top}%`;
                el.style.left = `${left}%`;
                el.style.animationDuration = `${duration}s`;
                el.style.animationDelay = `${Math.random()*5}s`;
                container.appendChild(el);
            }
        }
        document.addEventListener('DOMContentLoaded', createBackgroundElements);
    </script>
</body>
</html>