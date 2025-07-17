<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION["admin"])) {
    header("Location: backend/login.php");
    exit();
}

// Handle logout
if (isset($_GET["logout"])) {
    session_destroy();
    header("Location: logout.php");
    exit();
}

// Connect to MySQL
$conn = new mysqli("localhost", "root", "root", "icspl");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch contact submissions
$sql = "SELECT id, name, phone_number, gmail, requirements FROM users";
$result = $conn->query($sql);

// Fetch admin users
$sql_admins = "SELECT id, email, otp_expiry FROM admin_users";
$result_admins = $conn->query($sql_admins);

// ✅ Fetch admin login logs
$sql_logs = "SELECT id, email, login_time FROM admin_login_logs ORDER BY login_time DESC";
$result_logs = $conn->query($sql_logs);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background-color: #0f172a;
            color: #f8fafc;
            font-family: 'Inter', sans-serif;
            padding-bottom: 50px;
        }
        .header {
            background-color: #1e293b;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #334155;
        }
        .admin-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .admin-info img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #94a3b8;
        }
        .logout-btn {
            background-color: #ef4444;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .logout-btn:hover {
            background-color: #dc2626;
        }
        h1, h2 {
            text-align: center;
            margin: 40px 0 20px;
            font-size: 32px;
        }
        .search-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        #searchInput {
            padding: 12px 16px;
            width: 340px;
            border-radius: 10px;
            border: none;
            font-size: 16px;
            background-color: #1e293b;
            color: white;
            transition: all 0.3s;
        }
        #searchInput:focus {
            outline: none;
            box-shadow: 0 0 0 2px #3b82f6;
        }
        .table-container {
            width: 95%;
            max-width: 1100px;
            margin: 0 auto;
            background-color: #1e293b;
            border-radius: 12px;
            overflow-x: auto;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.5);
            margin-bottom: 40px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            overflow: hidden;
        }
        th, td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid #334155;
        }
        th {
            background-color: #0f172a;
            font-weight: 600;
            text-transform: uppercase;
            color: #38bdf8;
        }
        tr:hover td {
            background-color: #334155;
        }
        td {
            color: #f1f5f9;
        }
        @media screen and (max-width: 600px) {
            #searchInput { width: 90%; }
            h1, h2 { font-size: 24px; }
        }
    </style>
</head>
<body>

<div class="header">
    <div class="admin-info">
        <img src="https://img.freepik.com/premium-photo/3d-sales-manager-character-leading-with-animated-ambition_893571-11254.jpg" alt="Admin">
        <strong>Welcome, <?php echo htmlspecialchars($_SESSION["admin"]); ?></strong>
    </div>
    <a class="logout-btn" href="?logout=true">Logout</a>
</div>

<h1>📥 Contact Submissions</h1>

<div class="search-container">
    <input type="text" id="searchInput" placeholder="Search requirements...">
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Requirements</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row["id"]}</td>
                            <td>" . htmlspecialchars($row["name"]) . "</td>
                            <td>{$row["phone_number"]}</td>
                            <td>{$row["gmail"]}</td>
                            <td>" . htmlspecialchars($row["requirements"]) . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5' style='text-align:center;'>No submissions found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<h2>📅 Admin Login Logs</h2>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Log ID</th>
                <th>Admin Email</th>
                <th>Login Time</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result_logs && $result_logs->num_rows > 0) {
                while ($log = $result_logs->fetch_assoc()) {
                    echo "<tr>
                            <td>{$log["id"]}</td>
                            <td>{$log["email"]}</td>
                            <td>{$log["login_time"]}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='3' style='text-align:center;'>No login logs found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
    document.getElementById("searchInput").addEventListener("keyup", function () {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll("table tbody tr");

        rows.forEach(row => {
            const requirement = row.cells[4]?.textContent.toLowerCase() || "";
            row.style.display = requirement.includes(filter) ? "" : "none";
        });
    });
</script>

</body>
</html>
