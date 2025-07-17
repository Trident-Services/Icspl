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

    $message = "✅ Password reset successfully! <a href='login.php'>Login Now</a>";
}
?>

<!DOCTYPE html>
<html>
<head><title>Reset Password</title></head>
<body>
    <h2>Set New Password</h2>
    <form method="post">
        <input type="password" name="password" placeholder="New Password" required><br><br>
        <button type="submit">Reset Password</button>
    </form>
    <?php if ($message) echo "<p style='color:green;'>$message</p>"; ?>
</body>
</html>
