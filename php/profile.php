<?php
session_start();

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
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
try {
    $stmt = $pdo->prepare("SELECT bio, profile_picture FROM profiles WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    // If profile doesn't exist, initialize empty profile
    if (!$profile) {
        $profile = [
            'bio' => '',
            'profile_picture' => ''
        ];
    }
} catch (Exception $e) {
    die('Error fetching profile: ' . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bio = $_POST['bio'];
    $profile_picture = $_FILES['profile_picture']['name'];
    $profile_picture_tmp = $_FILES['profile_picture']['tmp_name'];
    $target_dir = "../uploads/"; // Adjust the path accordingly
    $target_file = $target_dir . basename($profile_picture);

    try {
        // Handle file upload
        if (!empty($profile_picture) && move_uploaded_file($profile_picture_tmp, $target_file)) {
            if ($profile['profile_picture']) {
                // Update existing profile
                $stmt = $pdo->prepare("UPDATE profiles SET bio = :bio, profile_picture = :profile_picture WHERE user_id = :user_id");
            } else {
                // Insert new profile
                $stmt = $pdo->prepare("INSERT INTO profiles (bio, profile_picture, user_id) VALUES (:bio, :profile_picture, :user_id)");
            }
            $stmt->bindParam(':bio', $bio);
            $stmt->bindParam(':profile_picture', $profile_picture);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $update_message = 'Profile updated successfully!';
                // Update $profile variable to reflect changes
                $profile['bio'] = $bio;
                $profile['profile_picture'] = $profile_picture;
                // Redirect to index.php
                header("Location: ../index.php");
                exit;
            } else {
                throw new Exception("Error updating profile: " . implode(", ", $stmt->errorInfo()));
            }
        } else {
            // No new file uploaded, update only bio
            if ($profile['bio']) {
                // Update existing profile
                $stmt = $pdo->prepare("UPDATE profiles SET bio = :bio WHERE user_id = :user_id");
            } else {
                // Insert new profile
                $stmt = $pdo->prepare("INSERT INTO profiles (bio, user_id) VALUES (:bio, :user_id)");
            }
            $stmt->bindParam(':bio', $bio);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $update_message = 'Profile updated successfully!';
                // Update $profile variable to reflect changes
                $profile['bio'] = $bio;
                // Redirect to index.php
                header("Location: ../index.php");
                exit;
            } else {
                throw new Exception("Error updating profile: " . implode(", ", $stmt->errorInfo()));
            }
        }
    } catch (Exception $e) {
        $update_message = $e->getMessage();
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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>MotoTrip</h1>
            <nav>
                <ul>
                    <li><a href="logout.php">Logout</a></li>
                    <li><a href="../index.php"><i class="fas fa-user"></i> Profile</a></li>
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
                    <textarea id="bio" name="bio"><?php echo htmlspecialchars($profile['bio']); ?></textarea>
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
            var bio = document.getElementById('bio').value.trim();
            if (bio === '') {
                alert('Bio cannot be empty');
                return false; // Prevent form submission
            }

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
