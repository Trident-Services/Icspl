<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "root", "icspl");

$result = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 0;
        }

        .header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            padding: 20px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h2 {
            margin: 0;
            font-size: 22px;
        }

        .logout {
            color: white;
            text-decoration: none;
            font-weight: bold;
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            transition: background 0.3s;
        }

        .logout:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .container {
            padding: 30px;
        }

        h3 {
            margin-bottom: 20px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 14px 16px;
            text-align: left;
        }

        th {
            background: #667eea;
            color: white;
            text-transform: uppercase;
            font-size: 14px;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        tr:hover {
            background: #eef2ff;
        }

        td {
            color: #333;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            th, td {
                padding: 10px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<div class="header">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION["admin"]); ?></h2>
    <a class="logout" href="logout.php">Logout</a>
</div>

<div class="container">
    <h3>Users Table Data:</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Gmail</th>
            <th>Requirements</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row["id"]); ?></td>
            <td><?php echo htmlspecialchars($row["name"]); ?></td>
            <td><?php echo htmlspecialchars($row["phone_number"]); ?></td>
            <td><?php echo htmlspecialchars($row["gmail"]); ?></td>
            <td><?php echo htmlspecialchars($row["requirements"]); ?></td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>
