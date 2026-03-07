<?php
session_start();
if (!isset($_SESSION['org_id'])) { header("Location: login.php"); exit(); }
include("../connect.php");
$org_id = $_SESSION['org_id'];

// Handle category filter
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$query = "SELECT * FROM employees WHERE org_id = $org_id";
if ($category_filter && $category_filter != 'all') {
    $query .= " AND category = '" . mysqli_real_escape_string($conn, $category_filter) . "'";
}
$employees = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employees | SmartPresence Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI',sans-serif; }
        :root { --primary:#2c3e50; --secondary:#8e44ad; --accent:#9b59b6; --light:#ecf0f1; }
        body { background: linear-gradient(135deg, var(--primary), #1a2530); color: var(--light); min-height:100vh; }
        .background-elements { position:fixed; top:0; left:0; width:100%; height:100%; z-index:-1; overflow:hidden; }
        .circle,.square,.triangle { position:absolute; animation:float 15s infinite ease-in-out; }
        .circle { border-radius:50%; background:rgba(255,255,255,0.05); }
        .square { background:rgba(142,68,173,0.05); animation:rotate 20s infinite linear; }
        .triangle { width:0; height:0; border-style:solid; border-color:transparent transparent rgba(155,89,182,0.05) transparent; animation:float 18s infinite reverse; }
        .container { max-width:1200px; margin:0 auto; padding:2rem; }
        .navbar { background:rgba(255,255,255,0.08); border-radius:15px; padding:1rem 2rem; backdrop-filter:blur(10px); border:1px solid rgba(255,255,255,0.1); display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem; }
        .logo { font-size:1.8rem; color:var(--accent); }
        .nav-links a { color:var(--light); text-decoration:none; padding:0.5rem 1rem; border-radius:30px; transition:all 0.3s; }
        .nav-links a:hover, .nav-links a.active { background:rgba(155,89,182,0.3); }
        .logout-btn { background:rgba(231,76,60,0.2); color:#e74c3c; border:none; padding:0.5rem 1.5rem; border-radius:30px; cursor:pointer; }
        .filter-bar { margin-bottom:1.5rem; }
        .filter-bar select, .filter-bar button { padding:0.5rem 1rem; border-radius:5px; border:none; background:rgba(255,255,255,0.1); color:var(--light); }
        .filter-bar button { background:var(--accent); cursor:pointer; }
        table { width:100%; background:rgba(255,255,255,0.05); border-radius:15px; padding:1rem; backdrop-filter:blur(10px); }
        th, td { padding:0.75rem; text-align:left; border-bottom:1px solid rgba(255,255,255,0.1); }
        th { color:var(--accent); }
        footer { text-align:center; padding:2rem; margin-top:2rem; border-top:1px solid rgba(255,255,255,0.1); color:#95a5a6; }
        @keyframes float { 0%,100%{transform:translate(0,0);} 25%{transform:translate(10px,15px);} 50%{transform:translate(-5px,20px);} 75%{transform:translate(15px,-10px);} }
        @keyframes rotate { from{transform:rotate(0deg);} to{transform:rotate(360deg);} }
    </style>
</head>
<body>
    <div class="background-elements">...</div>
    <div class="container">
        <div class="navbar">
            <div class="logo"><i class="fas fa-user-shield"></i> SmartPresence Admin</div>
            <div class="nav-links">
                <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="employees.php" class="active"><i class="fas fa-users"></i> Employees</a>
                <a href="attendance.php"><i class="fas fa-calendar-check"></i> Attendance</a>
                <a href="contact_messages.php"><i class="fas fa-envelope"></i> Messages</a>
            </div>
            <button class="logout-btn" onclick="window.location.href='logout.php'"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </div>

        <h2 style="margin-bottom:1rem;"><i class="fas fa-users"></i> Employee List</h2>
        <div class="filter-bar">
            <form method="get">
                <label>Filter by Category:</label>
                <select name="category">
                    <option value="all">All</option>
                    <option value="Unskilled" <?php if($category_filter=='Unskilled') echo 'selected'; ?>>Unskilled</option>
                    <option value="Skilled" <?php if($category_filter=='Skilled') echo 'selected'; ?>>Skilled</option>
                    <option value="Semi-skilled" <?php if($category_filter=='Semi-skilled') echo 'selected'; ?>>Semi-skilled</option>
                    <option value="Highly Skilled" <?php if($category_filter=='Highly Skilled') echo 'selected'; ?>>Highly Skilled</option>
                </select>
                <button type="submit"><i class="fas fa-filter"></i> Apply</button>
            </form>
        </div>

        <?php if (mysqli_num_rows($employees) > 0): ?>
        <table>
            <tr><th>ID</th><th>Name</th><th>Email</th><th>Mobile</th><th>Category</th><th>DOB</th></tr>
            <?php while ($emp = mysqli_fetch_assoc($employees)): ?>
            <tr>
                <td><?php echo $emp['emp_id']; ?></td>
                <td><?php echo htmlspecialchars($emp['empname']); ?></td>
                <td><?php echo htmlspecialchars($emp['email']); ?></td>
                <td><?php echo htmlspecialchars($emp['mobile']); ?></td>
                <td><?php echo htmlspecialchars($emp['category']); ?></td>
                <td><?php echo $emp['dob']; ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php else: ?>
        <p>No employees found.</p>
        <?php endif; ?>
        <footer>&copy; 2025 SmartPresence Admin</footer>
    </div>
    <script> // same background script as dashboard </script>
</body>
</html>
<?php $conn->close(); ?>