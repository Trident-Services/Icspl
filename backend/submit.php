<?php
// Connect to MySQL
$conn = mysqli_connect("localhost", "root", "root", "icspl");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if form is submitted with required fields
if (
    isset($_POST['name']) &&
    isset($_POST['phone']) &&
    isset($_POST['gmail'])
) {
    // Sanitize input data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $gmail = mysqli_real_escape_string($conn, $_POST['gmail']);
    $additional_message = isset($_POST['additional_message']) ? mysqli_real_escape_string($conn, $_POST['additional_message']) : '';
    
    // Process checkboxes
    $services = isset($_POST['services']) ? implode(", ", $_POST['services']) : 'None selected';
    $sectors = isset($_POST['sectors']) ? implode(", ", $_POST['sectors']) : 'None selected';
    
    // Email domain validation
    $allowed_domains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'protonmail.com', 'icloud.com'];
    $domain = substr(strrchr($gmail, "@"), 1);

    if (!in_array(strtolower($domain), $allowed_domains)) {
        header("Location: /public/pages/invalid-email.html");
        exit();
    }

    // Phone number validation
    if (!preg_match('/^\+\d{6,15}$/', $phone)) {
        header("Location: /public/pages/invalid-phone.html");
        exit();
    }

    // Prepare SQL statement with additional fields
    $stmt = $conn->prepare("INSERT INTO users (name, phone_number, gmail, services, sectors, additional_message) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $phone, $gmail, $services, $sectors, $additional_message);

    if ($stmt->execute()) {
        header("Location: /public/pages/thank-you.html");
        exit();
    } else {
        error_log("Database error: " . $stmt->error);
        header("Location: /public/pages/error.html");
        exit();
    }

    $stmt->close();
} else {
    header("Location: /public/pages/error.html");
    exit();
}

mysqli_close($conn);
?>