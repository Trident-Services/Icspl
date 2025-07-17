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
