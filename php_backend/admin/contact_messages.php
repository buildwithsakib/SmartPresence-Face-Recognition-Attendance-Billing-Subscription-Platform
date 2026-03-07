<?php
session_start();
if (!isset($_SESSION['org_id'])) {
    header("Location: login.php");
    exit();
}
include("../connect.php");
$org_id = $_SESSION['org_id'];

$single_msg = null;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = mysqli_query($conn, "SELECT * FROM contact_messages WHERE id = $id AND org_id = $org_id");
    $single_msg = mysqli_fetch_assoc($result);
    if (!$single_msg) {
        // Message not found or not belonging to this org
        header("Location: contact_messages.php");
        exit();
    }
} else {
    $msgs = mysqli_query($conn, "SELECT * FROM contact_messages WHERE org_id = $org_id ORDER BY created_at DESC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages | SmartPresence Admin</title>
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
        /* Page Header */
        h2 {
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        h2 i {
            color: var(--accent);
        }
        /* Table Styling */
        .messages-table {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 1.5rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        th {
            color: var(--accent);
            font-weight: 600;
            background: rgba(0, 0, 0, 0.2);
        }
        tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        .view-link {
            color: var(--accent);
            text-decoration: none;
            padding: 0.3rem 0.8rem;
            border-radius: 30px;
            background: rgba(155, 89, 182, 0.2);
            transition: all 0.3s;
        }
        .view-link:hover {
            background: rgba(155, 89, 182, 0.4);
            text-decoration: none;
        }
        /* Single Message View */
        .message-detail {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 2rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .message-field {
            margin-bottom: 1.5rem;
        }
        .message-field strong {
            display: block;
            color: var(--accent);
            margin-bottom: 0.3rem;
        }
        .message-field p {
            background: rgba(0, 0, 0, 0.2);
            padding: 0.8rem;
            border-radius: 8px;
            word-wrap: break-word;
        }
        .message-meta {
            color: #bdc3c7;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
        .back-to-list {
            margin-top: 2rem;
        }
        .back-to-list a {
            color: var(--accent);
            text-decoration: none;
            font-size: 1rem;
        }
        .back-to-list a:hover {
            text-decoration: underline;
        }
        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #bdc3c7;
        }
        .empty-state i {
            font-size: 4rem;
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
            table {
                font-size: 0.9rem;
            }
            th, td {
                padding: 0.5rem;
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
                <a href="attendance.php"><i class="fas fa-calendar-check"></i> Attendance</a>
                <a href="contact_messages.php" class="active"><i class="fas fa-envelope"></i> Messages</a>
            </div>
            <button class="logout-btn" onclick="window.location.href='logout.php'"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </div>

        <!-- Back to Dashboard Link (always visible) -->
        <div class="back-link">
            <a href="dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>

        <!-- Page Header -->
        <h2><i class="fas fa-envelope-open-text"></i> Contact Messages</h2>

        <?php if ($single_msg): ?>
            <!-- Single Message View -->
            <div class="message-detail">
                <div class="message-field">
                    <strong><i class="fas fa-user"></i> Name</strong>
                    <p><?php echo htmlspecialchars($single_msg['name']); ?></p>
                </div>
                <div class="message-field">
                    <strong><i class="fas fa-envelope"></i> Email</strong>
                    <p><?php echo htmlspecialchars($single_msg['email']); ?></p>
                </div>
                <div class="message-field">
                    <strong><i class="fas fa-tag"></i> Subject</strong>
                    <p><?php echo htmlspecialchars($single_msg['subject']); ?></p>
                </div>
                <div class="message-field">
                    <strong><i class="fas fa-comment"></i> Message</strong>
                    <p><?php echo nl2br(htmlspecialchars($single_msg['message'])); ?></p>
                </div>
                <div class="message-meta">
                    <i class="fas fa-clock"></i> Received on <?php echo date('d M Y, h:i A', strtotime($single_msg['created_at'])); ?>
                </div>
                <div class="back-to-list">
                    <a href="contact_messages.php"><i class="fas fa-arrow-left"></i> Back to Messages List</a>
                </div>
            </div>
        <?php else: ?>
            <!-- Messages List -->
            <div class="messages-table">
                <?php if (mysqli_num_rows($msgs) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($msg = mysqli_fetch_assoc($msgs)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($msg['name']); ?></td>
                            <td><?php echo htmlspecialchars($msg['email']); ?></td>
                            <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                            <td><?php echo date('d-m-Y H:i', strtotime($msg['created_at'])); ?></td>
                            <td><a href="?id=<?php echo $msg['id']; ?>" class="view-link"><i class="fas fa-eye"></i> View</a></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No messages yet.</p>
                </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

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