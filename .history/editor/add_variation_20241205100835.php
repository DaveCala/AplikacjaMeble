<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set content type to JSON
header('Content-Type: application/json');

// Try to handle the form data and interact with the database
try {
    // Connect to the database
    $conn = new mysqli('localhost', 'username', 'password', 'database');
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Get the form data
    $title = $_POST['title'] ?? '';
    $ean = $_POST['ean'] ?? '';
    $imagePath = null;

    // Handle file upload
    if (!empty($_FILES['main_image']['name'])) {
        $targetDir = 'uploads/';
        $imagePath = $targetDir . basename($_FILES['main_image']['name']);
        if (!move_uploaded_file($_FILES['main_image']['tmp_name'], $imagePath)) {
            throw new Exception("Failed to upload file.");
        }
    }

    // Insert variation data into the database
    $product_id = 1;  // Assuming you're always adding variations for product ID 1
    $stmt = $conn->prepare("INSERT INTO variations (product_id, title, main_image, ean, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("isss", $product_id, $title, $imagePath, $ean);
    
    if ($stmt->execute()) {
        $response = ['success' => true, 'message' => 'Variation added successfully.'];
    } else {
        throw new Exception("SQL Error: " . $stmt->error);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    // If an error occurs, return the error message
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

// Return the response as JSON
echo json_encode($response);
?>
