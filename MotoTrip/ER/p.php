<?php
session_start();

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}


// Include the database connection file
include 'db.php';

// Check if $pdo is defined
if (!isset($pdo)) {
    die('Database connection failed.');
}



$update_message = '';
$user_id = $_SESSION['user_id'];
$profile = [];



// Fetch the user profile information
$stmt = $pdo->prepare("SELECT bio, profile_picture FROM profiles WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$profile = $stmt->fetch(PDO::FETCH_ASSOC);




// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bio = $_POST['bio'];
    $profile_picture = $_FILES['profile_picture']['name'];
    // $stmt = $pdo->prepare("UPDATE profiles SET bio = 'test bio' WHERE user_id = 11");
    // $stmt->execute();
    // Handle file upload
    if (!empty($profile_picture)) {
        $profile_picture_tmp = $_FILES['profile_picture']['tmp_name'];
        $target_dir = "../uploads/"; // Adjust the path accordingly
        $target_file = $target_dir . basename($profile_picture);

        if (move_uploaded_file($profile_picture_tmp, $target_file)) {
            $user_id = 11;
            // File uploaded successfully, update database
            $stmt = $pdo->prepare("UPDATE profiles SET bio = :bio, profile_picture = :profile_picture WHERE user_id = :user_id");
            $stmt->bindParam(':bio', $bio);
            $stmt->bindParam(':profile_picture', $profile_picture);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            // $sql = "UPDATE your_table_name SET column1 = :bio, profile_picture = :profile_picture WHERE user_id = :user_id";
            // $stmt = $pdo->prepare($sql);

            // // Bind parameters
            // $stmt->bindParam(':column1', $value1);
            // $stmt->bindParam(':column2', $value2);
            // $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->execute()) {
                $update_message = 'Profile updated successfully!';
                // Update $profile variable to reflect changes
                $profile['bio'] = $bio;
                $profile['profile_picture'] = $profile_picture;
            } else {
                echo "Error updating profile: " . implode(", ", $stmt->errorInfo());
            }
        } else {
            echo "Error uploading file.";
        }
    } else {
        // No new file uploaded, update only bio
        $stmt = $pdo->prepare("UPDATE profiles SET bio = :bio WHERE user_id = :user_id");
        $stmt->bindParam(':bio', $bio);
        $stmt->bindParam(':user_id', $user_id);

        if ($stmt->execute()) {
            $update_message = 'Profile updated successfully!';
            // Update $profile variable to reflect changes
            $profile['bio'] = $bio;
        } else {
            echo "Error updating profile: " . implode(", ", $stmt->errorInfo());
        }
    }
}

?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MotoTrip - Profile</title>
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>
<body>
    <header>
        <div class="container">
            <h1>MotoTrip</h1>
            <nav>
                <ul>
                    <li><a href="php/logout.php">Logout</a></li>
                    <li><a href="../index.php"><i class="fas fa-dashboard"></i> Dashboard</a></li> <!-- Profile Icon Added -->
                </ul>
            </nav>
        </div>
    </header>
    <main>
        <section class="profile">
            <div class="container">
            <form action="profile.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                <h2>Profile</h2>
                <?php if (!empty($update_message)): ?>
                    <p class="update-message"><?php echo htmlspecialchars($update_message); ?></p>
                <?php endif; ?>
                <label for="bio">Bio:</label>
                <textarea id="bio" name="bio"><?php echo htmlspecialchars($profile['bio'] ?? ''); ?></textarea>
                <label for="profile_picture">Profile Picture:</label>
                <input type="file" id="profile_picture" name="profile_picture">
                <?php if (!empty($profile['profile_picture'])): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($profile['profile_picture']); ?>" alt="Profile Picture" width="100">
                <?php endif; ?>
                <br>
                <button type="submit">Update Profile</button>
            </form>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 MotoTrip. All rights reserved.</p>
        </div>
    </footer>

<script>
    function validateForm() {
        // Perform your validation here
        // Example: Check if the bio textarea is not empty

        var bio = document.getElementById('bio').value.trim();
        if (bio === '') {
            alert('Bio cannot be empty');
            return false; // Prevent form submission
        }

        // Example: Check if a file is selected for profile picture
        var profile_picture = document.getElementById('profile_picture').value.trim();
        if (profile_picture === '' && '<?php echo !empty($profile['profile_picture']) ? 'false' : 'true'; ?>' === 'true') {
            alert('Please select a profile picture');
            return false; // Prevent form submission
        }
        return true; // Allow form submission
    }    
</script>

</body>
</html>
