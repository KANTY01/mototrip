<?php
    // Start session
    session_start();

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        // Redirect to login page if not logged in
        header("Location: login.php");
        exit();
    }

    // Include the database connection file
    include './php/db.php';

    // Fetch profile data from database for the logged-in user
    $user_id = $_SESSION['user_id'];

    // Fetch user profile data
    $queryProfile = "SELECT bio, profile_picture FROM profiles WHERE user_id = :user_id";
    $stmtProfile = $pdo->prepare($queryProfile);
    $stmtProfile->execute(['user_id' => $user_id]);
    $profile = $stmtProfile->fetch(PDO::FETCH_ASSOC);

    // Fetch user data (username and email)
    $queryUser = "SELECT username, email FROM users WHERE id = :user_id";
    $stmtUser = $pdo->prepare($queryUser);
    $stmtUser->execute(['user_id' => $user_id]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

    // Check if profile data exists
    if (!$profile) {
        $profile = ['bio' => '', 'profile_picture' => ''];
    }

    // Close the database connection (optional as PDO closes on script end)
    //$pdo = null;

    // Debugging: Print the profile picture path
    // echo '<pre>'; print_r($profile['profile_picture']); echo '</pre>';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MotoTrip</title>
    <link rel="stylesheet" href="./css/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>
<body>

    <header>
        <div class="container">
            <h1>MotoTrip</h1>
            <nav>
                <ul>
                    <li><a href="php/logout.php">Logout</a></li>
                    <li><a href="./php/profile.php"><i class="fas fa-user"></i> Profile</a></li> <!-- Profile Icon Added -->
                    <li><a href="index.php"><i class="fas fa-dashboard"></i> Dashboard</a></li> <!-- Profile Icon Added -->
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="dashboard">
            <div class="container">
                <ul class="icon-list">
                    <li><a href="./php/all_meeting.php"><i class="fas fa-sitemap"></i> All Meeting</a></li>
                    <li><a href="./php/meetings.php"><i class="fas fa-users"></i>My Meeting</a></li>
                    <li><a href="./php/create_meeting.php"><i class="fas fa-add"></i>Create Meeting</a></li>
                </ul>
            </div>
        </section>

        <section class="dashboard1">
            <div class="container">
                <div class="profile-column">
                    <img src="./uploads/<?php echo htmlspecialchars($profile['profile_picture'] ?? ''); ?>" alt="Profile Image" class="profile-image">
                    <div class="profile-details">
                        <h3>Profile Photo</h3>
                     
                            <p><?php echo htmlspecialchars($profile['bio'] ?? ''); ?></p>
                     
                    </div>
                </div>
                <div class="button-list-column">
                    <ul class="icon-list1">
                        <li><strong>Email : </strong><?php echo htmlspecialchars($user['email']); ?></li>
                        <li><strong>User name : </strong><?php echo htmlspecialchars($user['username']); ?></li>
                    </ul>
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
