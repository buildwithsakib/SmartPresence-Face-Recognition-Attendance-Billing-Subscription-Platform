<?php
session_start();
if (!isset($_SESSION['org_id'])) {
    header("Location: login.php");
    exit();
}
include("../connect.php");
$org_id = $_SESSION['org_id'];

$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Get present employees for selected date
$present_emps = mysqli_query($conn, "
    SELECT e.empname, e.category, a.time_in 
    FROM attendance a 
    JOIN employees e ON a.emp_id = e.emp_id 
    WHERE a.org_id = $org_id AND a.date = '$date' AND a.status = 'present'
");

// Get absent employees for selected date
$absent_emps = mysqli_query($conn, "
    SELECT e.empname, e.category 
    FROM employees e 
    WHERE e.org_id = $org_id AND e.emp_id NOT IN (
        SELECT emp_id FROM attendance WHERE date = '$date' AND org_id = $org_id
    )
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance | SmartPresence Admin</title>
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
        /* Background Animation Elements */
        .background-elements {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }
        .circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            animation: float 15s infinite ease-in-out;
        }
        .square {
            position: absolute;
            background: rgba(142, 68, 173, 0.05);
            animation: rotate 20s infinite linear;
        }
        .triangle {
            position: absolute;
            width: 0;
            height: 0;
            border-style: solid;
            border-color: transparent transparent rgba(155, 89, 182, 0.05) transparent;
            animation: float 18s infinite ease-in-out reverse;
        }
        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            position: relative;
            z-index: 10;
        }
        /* Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 15px;
            padding: 1rem 2rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .logo {
            font-size: 1.8rem;
            color: var(--accent);
        }
        .nav-links {
            display: flex;
            gap: 1rem;
        }
        .nav-links a {
            color: var(--light);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 30px;
            transition: all 0.3s;
        }
        .nav-links a:hover,
        .nav-links a.active {
            background: rgba(155, 89, 182, 0.3);
        }
        .logout-btn {
            background: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .logout-btn:hover {
            background: rgba(231, 76, 60, 0.3);
        }
        /* Back button */
        .back-link {
            display: inline-block;
            margin-bottom: 1.5rem;
        }
        .back-link a {
            color: var(--accent);
            text-decoration: none;
            font-size: 1.1rem;
            transition: all 0.3s;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
        /* Filter bar */
        .filter-bar {
            margin-bottom: 2rem;
            background: rgba(255, 255, 255, 0.05);
            padding: 1.5rem;
            border-radius: 15px;
            backdrop-filter: blur(10px);
        }
        .filter-bar label {
            margin-right: 1rem;
            font-weight: 600;
            color: var(--accent);
        }
        .filter-bar input,
        .filter-bar select,
        .filter-bar button {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            border: none;
            background: rgba(255, 255, 255, 0.1);
            color: var(--light);
            font-size: 1rem;
            transition: all 0.3s;
        }
        .filter-bar input:focus,
        .filter-bar select:focus {
            outline: none;
            border: 1px solid var(--accent);
        }
        .filter-bar button {
            background: var(--accent);
            cursor: pointer;
            font-weight: 600;
            box-shadow: 0 5px 15px rgba(155, 89, 182, 0.4);
        }
        .filter-bar button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(155, 89, 182, 0.6);
        }
        /* Attendance sections */
        .attendance-grid {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
        }
        .attendance-section {
            flex: 1;
            min-width: 300px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 1.5rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .attendance-section h3 {
            margin-bottom: 1rem;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .attendance-section h3 i {
            font-size: 1.5rem;
        }
        .present-header {
            color: #2ecc71;
        }
        .absent-header {
            color: #e74c3c;
        }
        .employee-list {
            list-style: none;
            max-height: 400px;
            overflow-y: auto;
        }
        .employee-list li {
            padding: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .employee-list li:last-child {
            border-bottom: none;
        }
        .employee-name {
            font-weight: 500;
        }
        .employee-category {
            font-size: 0.9rem;
            color: var(--accent);
        }
        .time-badge {
            background: rgba(46, 204, 113, 0.2);
            color: #2ecc71;
            padding: 0.2rem 0.8rem;
            border-radius: 30px;
            font-size: 0.9rem;
        }
        /* Empty state */
        .empty-state {
            text-align: center;
            color: #bdc3c7;
            padding: 2rem;
        }
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        /* Footer */
        footer {
            text-align: center;
            padding: 2rem;
            margin-top: 3rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: #95a5a6;
        }
        /* Animations */
        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            25% { transform: translate(10px, 15px); }
            50% { transform: translate(-5px, 20px); }
            75% { transform: translate(15px, -10px); }
        }
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        /* Responsive */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 1rem;
            }
            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }
            .attendance-grid {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Background Animation Elements -->
    <div class="background-elements">
        <div class="circle" style="width: 300px; height: 300px; top: 10%; left: 5%;"></div>
        <div class="circle" style="width: 150px; height: 150px; top: 70%; left: 80%;"></div>
        <div class="square" style="width: 200px; height: 200px; top: 20%; left: 85%;"></div>
        <div class="square" style="width: 120px; height: 120px; top: 65%; left: 10%;"></div>
        <div class="triangle" style="border-width: 0 100px 170px 100px; top: 40%; left: 25%;"></div>
        <div class="triangle" style="border-width: 0 70px 120px 70px; top: 80%; left: 70%;"></div>
    </div>

    <div class="container">
        <!-- Navbar -->
        <div class="navbar">
            <div class="logo"><i class="fas fa-user-shield"></i> SmartPresence Admin</div>
            <div class="nav-links">
                <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="employees.php"><i class="fas fa-users"></i> Employees</a>
                <a href="attendance.php" class="active"><i class="fas fa-calendar-check"></i> Attendance</a>
                <a href="contact_messages.php"><i class="fas fa-envelope"></i> Messages</a>
            </div>
            <button class="logout-btn" onclick="window.location.href='logout.php'"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </div>

        <!-- Back to Dashboard Link -->
        <div class="back-link">
            <a href="dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>

        <!-- Page Header -->
        <h2 style="margin-bottom: 1.5rem;"><i class="fas fa-calendar-alt"></i> Attendance for <span style="color: var(--accent);"><?php echo $date; ?></span></h2>

        <!-- Date Filter -->
        <div class="filter-bar">
            <form method="get">
                <label for="date"><i class="fas fa-calendar"></i> Select Date:</label>
                <input type="date" name="date" id="date" value="<?php echo $date; ?>">
                <button type="submit"><i class="fas fa-search"></i> View Attendance</button>
            </form>
        </div>

        <!-- Attendance Lists -->
        <div class="attendance-grid">
            <!-- Present Employees -->
            <div class="attendance-section">
                <h3 class="present-header"><i class="fas fa-user-check"></i> Present (<?php echo mysqli_num_rows($present_emps); ?>)</h3>
                <?php if (mysqli_num_rows($present_emps) > 0): ?>
                <ul class="employee-list">
                    <?php while ($p = mysqli_fetch_assoc($present_emps)): ?>
                    <li>
                        <div>
                            <span class="employee-name"><?php echo htmlspecialchars($p['empname']); ?></span>
                            <span class="employee-category">(<?php echo htmlspecialchars($p['category']); ?>)</span>
                        </div>
                        <span class="time-badge"><i class="fas fa-clock"></i> <?php echo $p['time_in']; ?></span>
                    </li>
                    <?php endwhile; ?>
                </ul>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-user-slash"></i>
                    <p>No employees marked present on this date.</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Absent Employees -->
            <div class="attendance-section">
                <h3 class="absent-header"><i class="fas fa-user-slash"></i> Absent (<?php echo mysqli_num_rows($absent_emps); ?>)</h3>
                <?php if (mysqli_num_rows($absent_emps) > 0): ?>
                <ul class="employee-list">
                    <?php while ($a = mysqli_fetch_assoc($absent_emps)): ?>
                    <li>
                        <span class="employee-name"><?php echo htmlspecialchars($a['empname']); ?></span>
                        <span class="employee-category"><?php echo htmlspecialchars($a['category']); ?></span>
                    </li>
                    <?php endwhile; ?>
                </ul>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-user-check"></i>
                    <p>No absent employees on this date. Everyone is present!</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <footer>
            <p>&copy; 2025 SmartPresence Admin Panel | All Rights Reserved</p>
        </footer>
    </div>

    <script>
        function createBackgroundElements() {
            const container = document.querySelector('.background-elements');
            const types = ['circle', 'square', 'triangle'];
            const colors = [
                'rgba(155, 89, 182, 0.05)',
                'rgba(142, 68, 173, 0.05)',
                'rgba(52, 152, 219, 0.05)'
            ];
            for (let i = 0; i < 8; i++) {
                const type = types[Math.floor(Math.random() * types.length)];
                const el = document.createElement('div');
                el.className = type;
                const size = Math.random() * 150 + 50;
                const top = Math.random() * 100;
                const left = Math.random() * 100;
                const duration = Math.random() * 20 + 10;
                if (type === 'triangle') {
                    const borderWidth = size / 2;
                    el.style.borderWidth = `0 ${borderWidth}px ${size}px ${borderWidth}px`;
                    el.style.borderColor = `transparent transparent ${colors[Math.floor(Math.random() * colors.length)]} transparent`;
                } else {
                    el.style.width = `${size}px`;
                    el.style.height = `${size}px`;
                    el.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                }
                el.style.top = `${top}%`;
                el.style.left = `${left}%`;
                el.style.animationDuration = `${duration}s`;
                el.style.animationDelay = `${Math.random() * 5}s`;
                container.appendChild(el);
            }
        }
        document.addEventListener('DOMContentLoaded', createBackgroundElements);
    </script>
</body>
</html>
<?php $conn->close(); ?>