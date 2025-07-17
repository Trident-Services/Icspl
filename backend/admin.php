// login.php
<?php
session_start();
$conn = new mysqli("localhost", "root", "root", "icspl");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT password FROM admin_users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION["admin"] = $email;
            header("Location: admin.php");
            exit();
        } else {
            $error = "❌ Invalid password.";
        }
    } else {
        $error = "⚠️ Email not found.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
</head>
<body>
    <div class="login-box">
        <h2>Admin Panel Login</h2>
        <form method="post">
            <input type="email" name="email" placeholder="Email" required><br><br>
            <input type="password" name="password" placeholder="Password" required><br><br>
            <button type="submit">Login</button>
        </form>
        <div>
            <a href="forgot_password.php">Forgot Password?</a>
        </div>
        <?php if (!empty($error)) echo "<div style='color:red;'>$error</div>"; ?>
    </div>
</body>
</html>


// forgot_password.php
<?php
session_start();
$conn = new mysqli("localhost", "root", "root", "icspl");
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $otp = rand(100000, 999999);
    $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

    $stmt = $conn->prepare("SELECT id FROM admin_users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt = $conn->prepare("UPDATE admin_users SET otp = ?, otp_expiry = ? WHERE email = ?");
        $stmt->bind_param("sss", $otp, $expiry, $email);
        $stmt->execute();

        $subject = "OTP for Password Reset";
        $body = "Your OTP is: $otp. It expires in 10 minutes.";
        $headers = "From: no-reply@icspl.com";

        if (mail($email, $subject, $body, $headers)) {
            $_SESSION['reset_email'] = $email;
            header("Location: verify_otp.php");
            exit();
        } else {
            $message = "❌ OTP sending failed.";
        }
    } else {
        $message = "⚠️ Email not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Forgot Password</title></head>
<body>
    <h2>Forgot Password</h2>
    <form method="post">
        <input type="email" name="email" placeholder="Enter your registered email" required><br><br>
        <button type="submit">Send OTP</button>
    </form>
    <?php if ($message) echo "<p style='color:red;'>$message</p>"; ?>
</body>
</html>


// verify_otp.php
<?php
session_start();
$conn = new mysqli("localhost", "root", "root", "icspl");

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_SESSION['reset_email'];
    $otp = $_POST['otp'];

    $stmt = $conn->prepare("SELECT otp, otp_expiry FROM admin_users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($dbOtp, $expiry);
    $stmt->fetch();
    $stmt->close();

    if ($otp === $dbOtp && strtotime($expiry) >= time()) {
        $_SESSION['verified'] = true;
        header("Location: reset_password.php");
        exit();
    } else {
        $message = "❌ Invalid or expired OTP.";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Verify OTP</title></head>
<body>
    <h2>Enter OTP</h2>
    <form method="post">
        <input type="text" name="otp" placeholder="Enter OTP" required><br><br>
        <button type="submit">Verify</button>
    </form>
    <?php if ($message) echo "<p style='color:red;'>$message</p>"; ?>
</body>
</html>


// reset_password.php
<?php
session_start();
$conn = new mysqli("localhost", "root", "root", "icspl");

if (!isset($_SESSION['reset_email']) || !isset($_SESSION['verified'])) {
    header("Location: forgot_password.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_SESSION['reset_email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE admin_users SET password = ?, otp = NULL, otp_expiry = NULL WHERE email = ?");
    $stmt->bind_param("ss", $password, $email);
    $stmt->execute();

    session_unset();
    session_destroy();

    $message = "✅ Password has been reset successfully! <a href='login.php'>Login</a>";
}
?>

<!DOCTYPE html>
<html>
<head><title>Reset Password</title></head>
<body>
    <h2>Reset Password</h2>
    <form method="post">
        <input type="password" name="password" placeholder="New Password" required><br><br>
        <button type="submit">Reset Password</button>
    </form>
    <?php if ($message) echo "<p style='color:green;'>$message</p>"; ?>
</body>
</html>
