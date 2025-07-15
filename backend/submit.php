<?php
// Connect to MySQL
$conn = mysqli_connect("localhost", "root", "root", "icspl");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if form is submitted with all required fields
if (isset($_POST['name'], $_POST['phone'], $_POST['gmail'], $_POST['requirements'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $gmail = $_POST['gmail'];
    $requirements = $_POST['requirements'];

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO users (name, phone_number, gmail, requirements) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $phone, $gmail, $requirements);

    if ($stmt->execute()) {
        echo "✅ Message submitted successfully!";
    } else {
        echo "❌ Error: " . $stmt->error;
    }

    $stmt->close(); // Always close the statement
} else {
    echo "⚠️ All fields are required.";
}

mysqli_close($conn);
?>
