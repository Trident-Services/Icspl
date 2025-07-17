<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}

// Handle logout
if (isset($_GET["logout"])) {
    session_destroy();
    header("Location: login.php");
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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Contact Submissions</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            background-color: #121212;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: #1f1f1f;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .admin-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #555;
        }

        .logout-btn {
            background-color: #e53e3e;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #c53030;
        }

        h1 {
            text-align: center;
            margin: 40px 0 20px;
            font-size: 28px;
        }

        .table-container {
            width: 95%;
            max-width: 1100px;
            margin: 0 auto 40px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #1e1e1e;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.3);
        }

        th, td {
            padding: 14px 16px;
            border-bottom: 1px solid #333;
            text-align: left;
        }

        th {
            background-color: #222;
            font-weight: bold;
        }

        tr:hover {
            background-color: #2a2a2a;
        }

        @media screen and (max-width: 600px) {
            th, td {
                padding: 10px;
                font-size: 14px;
            }

            h1 {
                font-size: 22px;
            }

            .logout-btn {
                padding: 6px 12px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="admin-info">
            <img src="https://img.freepik.com/premium-photo/3d-sales-manager-character-leading-with-animated-ambition_893571-11254.jpg" alt="Admin">
            <strong>Welcome, <?php echo $_SESSION["admin"]; ?></strong>
        </div>
        <a class="logout-btn" href="?logout=true">Logout</a>
    </div>

    <h1>📥 Contact Submissions</h1>

    <div class="table-container">
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Requirements</th>
            </tr>
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row["id"]}</td>
                            <td>{$row["name"]}</td>
                            <td>{$row["phone_number"]}</td>
                            <td>{$row["gmail"]}</td>
                            <td>{$row["requirements"]}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5' style='text-align:center;'>No submissions found.</td></tr>";
            }
            $conn->close();
            ?>
        </table>
    </div>

</body>
</html>
