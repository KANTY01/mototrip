<?php
session_start();
require_once 'db.php';

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $meeting_id = $_POST['meeting_id'];
    $title = $_POST['title'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $user_id = $_SESSION['user_id'];
    
    // Handle file upload
    $image_path = null;
    if ($_FILES["image"]["error"] == UPLOAD_ERR_OK) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["image"]["size"] > 500000) { // 500KB limit
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                // File is uploaded successfully
                $image_path = basename($_FILES["image"]["name"]); // Save the file name in the database
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }

    // Validate input (basic validation, you can add more)
    if (!empty($title) && !empty($date) && !empty($time) && !empty($location) && !empty($description)) {
        // Prepare the SQL statement
        $sql = "UPDATE meetings SET title = :title, date = :date, time = :time, location = :location, description = :description";
        if ($image_path !== null) {
            $sql .= ", image = :image";
        }
        $sql .= " WHERE id = :meeting_id AND user_id = :user_id";

        // Connect to the database and prepare the statement
        try {
            $stmt = $pdo->prepare($sql);

            // Bind parameters
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':time', $time);
            $stmt->bindParam(':location', $location);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':meeting_id', $meeting_id);
            $stmt->bindParam(':user_id', $user_id);
            if ($image_path !== null) {
                $stmt->bindParam(':image', $image_path);
            }

            // Execute the statement
            if ($stmt->execute()) {
                // Redirect to a success page or display a success message
                header("Location: meetings.php");
                exit();
            } else {
                // Handle error
                echo "Error: Could not execute the query.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "All fields are required.";
    }
}
?>
