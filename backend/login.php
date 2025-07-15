<?php
session_start();
$conn = new mysqli("localhost", "root", "root", "icspl");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT password FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION["admin"] = $username;
            header("Location: admin.php");
            exit();
        } else {
            $error = "❌ Invalid password.";
        }
    } else {
        $error = "⚠️ User not found.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            height: 100vh;
            align-items: center;
            justify-content: center;
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-box {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }

        .login-box h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            outline: none;
            font-size: 15px;
            transition: 0.3s;
        }

        .form-group input:focus {
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.4);
        }

        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s ease-in-out;
        }

        button:hover {
            transform: scale(1.02);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .error {
            margin-top: 15px;
            text-align: center;
            color: red;
            font-size: 14px;
        }

        .login-icon {
            text-align: center;
            font-size: 40px;
            color: #667eea;
            margin-bottom: 15px;
        }

    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-icon">🔐</div>
        <h2>Admin Panel Login</h2>
        <form method="post">
            <div class="form-group">
                <input type="text" name="username" placeholder="👤 Username" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="🔒 Password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
    </div>
</body>
</html>
