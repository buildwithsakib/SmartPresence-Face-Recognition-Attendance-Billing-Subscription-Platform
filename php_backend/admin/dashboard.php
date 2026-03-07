<?php
session_start();
if (!isset($_SESSION['org_id'])) {
    header("Location: login.php");
    exit();
}
include("../connect.php");
$org_id = $_SESSION['org_id'];

// Get total employees
$total_emp = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM employees WHERE org_id = $org_id"))['count'];

// Get today's date
$today = date('Y-m-d');

// Get present count
$present = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) as count FROM attendance 
    WHERE org_id = $org_id AND date = '$today' AND status = 'present'
"))['count'];

// Get absent count (total - present)
$absent = $total_emp - $present;

// Category counts
$categories = ['Unskilled','Skilled','Semi-skilled','Highly Skilled'];
$cat_counts = [];
foreach ($categories as $cat) {
    $count = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT COUNT(*) as c FROM employees WHERE org_id = $org_id AND category = '$cat'
    "))['c'];
    $cat_counts[$cat] = $count;
}

// Recent contact messages (last 5)
$msgs = mysqli_query($conn, "
    SELECT * FROM contact_messages WHERE org_id = $org_id 
    ORDER BY created_at DESC LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | SmartPresence</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI',sans-serif; }
        :root {
            --primary: #2c3e50;
            --secondary: #8e44ad;
            --accent: #9b59b6;
            --light: #ecf0f1;
            --dark: #2c3e50;
        }
        body {
            background: linear-gradient(135deg, var(--primary), #1a2530);
            color: var(--light);
            min-height: 100vh;
            position: relative;
        }
        .background-elements {
            position: fixed; top:0; left:0; width:100%; height:100%; z-index:-1; overflow:hidden;
        }
        .circle { position:absolute; border-radius:50%; background:rgba(255,255,255,0.05); animation:float 15s infinite ease-in-out; }
        .square { position:absolute; background:rgba(142,68,173,0.05); animation:rotate 20s infinite linear; }
        .triangle { position:absolute; width:0; height:0; border-style:solid; border-color:transparent transparent rgba(155,89,182,0.05) transparent; animation:float 18s infinite ease-in-out reverse; }
        .container { max-width:1200px; margin:0 auto; padding:2rem; }
        /* Navbar */
        .navbar {
            background: rgba(255,255,255,0.08);
            border-radius: 15px;
            padding: 1rem 2rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .logo { font-size:1.8rem; color:var(--accent); }
        .nav-links { display:flex; gap:1rem; }
        .nav-links a {
            color: var(--light);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 30px;
            transition: all 0.3s;
        }
        .nav-links a:hover, .nav-links a.active { background: rgba(155,89,182,0.3); }
        .logout-btn {
            background: rgba(231,76,60,0.2);
            color: #e74c3c;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .logout-btn:hover { background: rgba(231,76,60,0.3); }
        /* Stats cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: rgba(255,255,255,0.08);
            border-radius: 15px;
            padding: 1.5rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            text-align: center;
            transition: all 0.3s;
        }
        .stat-card:hover { transform: translateY(-5px); background: rgba(155,89,182,0.15); }
        .stat-icon { font-size: 2.5rem; color:var(--accent); margin-bottom:0.5rem; }
        .stat-number { font-size: 2rem; font-weight:bold; background:linear-gradient(to right,var(--accent),var(--secondary)); -webkit-background-clip:text; background-clip:text; color:transparent; }
        .stat-title { color:#bdc3c7; }
        /* Category cards */
        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .category-card {
            background: rgba(255,255,255,0.05);
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
        }
        .category-name { font-size:1.1rem; color:var(--accent); }
        .category-count { font-size:1.8rem; font-weight:bold; }
        /* Recent messages */
        .messages-table {
            background: rgba(255,255,255,0.05);
            border-radius: 15px;
            padding: 1.5rem;
            backdrop-filter: blur(10px);
        }
        .messages-table h3 { margin-bottom:1rem; color:var(--accent); }
        table { width:100%; border-collapse:collapse; }
        th, td { padding:0.75rem; text-align:left; border-bottom:1px solid rgba(255,255,255,0.1); }
        th { color:var(--accent); font-weight:600; }
        tr:hover { background:rgba(255,255,255,0.05); }
        .view-link { color:var(--accent); text-decoration:none; }
        .view-link:hover { text-decoration:underline; }
        footer { text-align:center; padding:2rem; margin-top:2rem; border-top:1px solid rgba(255,255,255,0.1); color:#95a5a6; }
        @keyframes float { 0%,100%{transform:translate(0,0);} 25%{transform:translate(10px,15px);} 50%{transform:translate(-5px,20px);} 75%{transform:translate(15px,-10px);} }
        @keyframes rotate { from{transform:rotate(0deg);} to{transform:rotate(360deg);} }
    </style>
</head>
<body>
    <div class="background-elements">
        <div class="circle" style="width:300px;height:300px;top:10%;left:5%;"></div>
        <div class="circle" style="width:150px;height:150px;top:70%;left:80%;"></div>
        <div class="square" style="width:200px;height:200px;top:20%;left:85%;"></div>
        <div class="triangle" style="border-width:0 100px 170px 100px;top:40%;left:25%;"></div>
    </div>
    <div class="container">
        <div class="navbar">
            <div class="logo"><i class="fas fa-user-shield"></i> SmartPresence Admin</div>
            <div class="nav-links">
                <a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
                <a href="employees.php"><i class="fas fa-users"></i> Employees</a>
                <a href="attendance.php"><i class="fas fa-calendar-check"></i> Attendance</a>
                <a href="contact_messages.php"><i class="fas fa-envelope"></i> Messages</a>
            </div>
            <button class="logout-btn" onclick="window.location.href='logout.php'"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-number"><?php echo $total_emp; ?></div>
                <div class="stat-title">Total Employees</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-user-check"></i></div>
                <div class="stat-number"><?php echo $present; ?></div>
                <div class="stat-title">Present Today</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-user-slash"></i></div>
                <div class="stat-number"><?php echo $absent; ?></div>
                <div class="stat-title">Absent Today</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-number"><?php echo date('d M Y'); ?></div>
                <div class="stat-title">Current Date</div>
            </div>
        </div>

        <h2 style="margin-bottom:1rem; color:var(--accent);"><i class="fas fa-chart-pie"></i> Category Breakdown</h2>
        <div class="category-grid">
            <?php foreach ($cat_counts as $cat => $count): ?>
            <div class="category-card">
                <div class="category-name"><?php echo htmlspecialchars($cat); ?></div>
                <div class="category-count" style="background:linear-gradient(to right,var(--accent),var(--secondary)); -webkit-background-clip:text; background-clip:text; color:transparent;"><?php echo $count; ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="messages-table">
            <h3><i class="fas fa-envelope-open-text"></i> Recent Contact Messages</h3>
            <?php if (mysqli_num_rows($msgs) > 0): ?>
            <table>
                <tr><th>Name</th><th>Email</th><th>Subject</th><th>Date</th><th></th></tr>
                <?php while ($msg = mysqli_fetch_assoc($msgs)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($msg['name']); ?></td>
                    <td><?php echo htmlspecialchars($msg['email']); ?></td>
                    <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                    <td><?php echo date('d-m-Y', strtotime($msg['created_at'])); ?></td>
                    <td><a href="contact_messages.php?id=<?php echo $msg['id']; ?>" class="view-link">View</a></td>
                </tr>
                <?php endwhile; ?>
            </table>
            <?php else: ?>
            <p>No messages yet.</p>
            <?php endif; ?>
        </div>
        <footer>&copy; 2025 SmartPresence Admin Panel</footer>
    </div>
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
<?php $conn->close(); ?>