<?php
    // create-meeting.php
    session_start();
    require_once 'db.php'; // Adjust the path to your db.php

    // Enable error reporting
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Redirect to login page if user is not logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit();
    }

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get form data
        $title = $_POST['title'];
        $date = $_POST['date'];
        $time = $_POST['time'];
        $location = $_POST['location'];
        $description = $_POST['description'];
        $user_id = $_SESSION['user_id'];

        // Handle file upload
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
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                // File is uploaded successfully
                $image_path = basename($_FILES["image"]["name"]); // Save the file name in the database

                // Validate input (basic validation, you can add more)
                if (!empty($title) && !empty($date) && !empty($time) && !empty($location) && !empty($description)) {
                    // Prepare the SQL statement
                    $sql = "INSERT INTO meetings (title, date, time, location, description, user_id, image) VALUES (:title, :date, :time, :location, :description, :user_id, :image)";

                    // Connect to the database and prepare the statement
                    try {
                        $stmt = $pdo->prepare($sql);

                        // Bind parameters
                        $stmt->bindParam(':title', $title);
                        $stmt->bindParam(':date', $date);
                        $stmt->bindParam(':time', $time);
                        $stmt->bindParam(':location', $location);
                        $stmt->bindParam(':description', $description);
                        $stmt->bindParam(':user_id', $user_id);
                        $stmt->bindParam(':image', $image_path);

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
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Meeting - MotoTrip</title>
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <header>
        <div class="container">
            <h1>MotoTrip</h1>
            <nav>
                <ul>
                    <li><a href="logout.php">Logout</a></li>
                    <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li> <!-- Profile Icon Added -->
                    <li><a href="../index.php"><i class="fas fa-dashboard"></i> Dashboard</a></li> <!-- Profile Icon Added -->
                </ul>
            </nav>
        </div>
    </header>

    <main>
    <section class="dashboard">
            <div class="container">
                <ul class="icon-list">
                    <li><a href="all_meeting.php"><i class="fas fa-sitemap"></i> All Meeting</a></li>
                    <li><a href="meetings.php"><i class="fas fa-users"></i>My Meeting</a></li>
                </ul>
            </div>
        </section>

        <section class="create-meeting">
            <div class="container">
                <h1>Create Meeting</h1>
                <div class="form-container">
                    <form action="create_meeting.php" method="POST" enctype="multipart/form-data">
                        <label for="title">Meeting Title</label>
                        <input type="text" id="title" name="title" required>

                        <label for="date">Date</label>
                        <input type="date" id="date" name="date" required>

                        <label for="time">Time</label>
                        <input type="time" id="time" name="time" required>

                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" required>

                        <label for="description">Description</label>
                        <textarea id="description" name="description" required></textarea>

                        <label for="image">Meeting Image</label>
                        <input type="file" id="image" name="image" accept="image/*">

                        <button type="submit">Create Meeting</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 MotoTrip. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
