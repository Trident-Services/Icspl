<?php
session_start();

// Auto-logout after 100 seconds (you may want to change it back to 1200 for 20 mins)
$timeout_duration = 100;

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: backend/login.php?timeout=1");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

if (!isset($_SESSION["admin"])) {
    header("Location: backend/login.php");
    exit();
}

if (isset($_GET["logout"])) {
    session_destroy();
    header("Location: logout.php");
    exit();
}

// DB Connection
$conn = new mysqli("localhost", "root", "root", "icspl");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query submissions (removed `requirements`)
$sql = "SELECT id, name, phone_number, gmail, services, sectors, additional_message FROM users";
$result = $conn->query($sql);

// Query login logs
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
        #searchInput, #logDatePicker {
            padding: 12px 16px;
            width: 340px;
            border-radius: 10px;
            border: none;
            font-size: 16px;
            background-color: #1e293b;
            color: white;
            transition: all 0.3s;
        }
        #searchInput:focus, #logDatePicker:focus {
            outline: none;
            box-shadow: 0 0 0 2px #3b82f6;
        }
        .table-container {
            width: 98%;
            max-width: 1400px;
            margin: 0 auto 40px;
            background-color: #1e293b;
            border-radius: 12px;
            overflow-x: auto;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.5);
        }
        table {
            width: 100%;
            border-collapse: collapse;
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
            #searchInput, #logDatePicker { width: 90%; }
            h1, h2 { font-size: 24px; }
        }
    </style>
</head>
<body>

<div class="header">
    <div class="admin-info">
        <img src="https://img.freepik.com/premium-photo/3d-sales-manager-character-leading-with-animated-ambition_893571-11254.jpg" alt="Admin">
        <strong>Welcome, <?php echo htmlspecialchars($_SESSION["admin"] ?? 'Admin'); ?></strong>
    </div>
    <a class="logout-btn" href="?logout=true">Logout</a>
</div>

<h1>📥 Contact Submissions</h1>

<div class="search-container">
    <input type="text" id="searchInput" placeholder="Search email or name...">
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Services</th>
                <th>Sectors</th>
                <th>Additional Message</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row["id"]) . "</td>
                            <td>" . htmlspecialchars($row["name"]) . "</td>
                            <td>" . htmlspecialchars($row["phone_number"]) . "</td>
                            <td>" . htmlspecialchars($row["gmail"]) . "</td>
                            <td>" . htmlspecialchars($row["services"]) . "</td>
                            <td>" . htmlspecialchars($row["sectors"]) . "</td>
                            <td>" . htmlspecialchars($row["additional_message"]) . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='7' style='text-align:center;'>No submissions found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<h2>📅 Admin Login Logs</h2>

<div class="search-container">
    <input type="date" id="logDatePicker" />
</div>

<div class="table-container">
    <table id="logTable">
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
                            <td>" . htmlspecialchars($log["id"]) . "</td>
                            <td>" . htmlspecialchars($log["email"]) . "</td>
                            <td>" . htmlspecialchars($log["login_time"]) . "</td>
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
    // Filter Contact Submissions (by name/email)
    document.getElementById("searchInput").addEventListener("keyup", function () {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll("table tbody tr");

        rows.forEach(row => {
            const name = row.cells[1]?.textContent.toLowerCase() || "";
            const email = row.cells[3]?.textContent.toLowerCase() || "";
            row.style.display = (name.includes(filter) || email.includes(filter)) ? "" : "none";
        });
    });

    // Filter Admin Logs by Date
    document.getElementById("logDatePicker").addEventListener("change", function () {
        const selectedDate = this.value;
        const rows = document.querySelectorAll("#logTable tbody tr");

        rows.forEach(row => {
            const loginTime = row.cells[2]?.textContent || "";
            const logDate = loginTime.split(" ")[0];
            row.style.display = (!selectedDate || logDate === selectedDate) ? "" : "none";
        });
    });
</script>

</body>
</html>
