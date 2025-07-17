<?php
// Connect to MySQL
$conn = mysqli_connect("localhost", "root", "root", "icspl");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if form is submitted with all required fields
if (
    isset($_POST['name']) &&
    isset($_POST['phone']) &&
    isset($_POST['gmail']) &&
    isset($_POST['requirements'])
) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $gmail = $_POST['gmail'];
    $requirements = $_POST['requirements'];

    // ✅ Email domain validation
    $allowed_domains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'protonmail.com', 'icloud.com'];
    $domain = substr(strrchr($gmail, "@"), 1);

    if (!in_array(strtolower($domain), $allowed_domains)) {
        // ❌ Invalid email domain — redirect to custom warning page
        header("Location: /public/pages/invalid-email.html");
        exit();
    }

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO users (name, phone_number, gmail, requirements) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $phone, $gmail, $requirements);

    if ($stmt->execute()) {
        // ✅ Redirect silently on success
        header("Location: /public/pages/thank-you.html");
        exit();
    } else {
        // ❌ Log error internally, redirect to generic error page
        error_log("Database error: " . $stmt->error);
        header("Location: /public/pages/error.html");
        exit();
    }

    $stmt->close();
} else {
    // ⚠️ Redirect if any required field is missing
    header("Location: /public/pages/error.html");
    exit();
}

// Close the DB connection
mysqli_close($conn);
?>
