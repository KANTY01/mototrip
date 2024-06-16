<?php
    session_start();
    require_once 'db.php';

    // Redirect to login page if user is not logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit();
    }

    // Get meeting ID from query parameter
    $meeting_id = $_GET['id'];

    // Fetch meeting data from the database
    $query = "SELECT * FROM meetings WHERE id = :meeting_id AND user_id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['meeting_id' => $meeting_id, 'user_id' => $_SESSION['user_id']]);
    $meeting = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if meeting data exists
    if (!$meeting) {
        die("Meeting not found.");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Meeting - MotoTrip</title>
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
                    <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                    <li><a href="../index.php"><i class="fas fa-dashboard"></i> Dashboard</a></li> 
                </ul>
            </nav>
        </div>
    </header>
    <main>
        <section class="dashboard">
            <div class="container">
                <ul class="icon-list">
                    <li><a href="all_meeting.php"><i class="fas fa-home"></i> All Meeting</a></li>
                    <li><a href="meetings.php"><i class="fas fa-users"></i>My Meeting</a></li>
                    <li><a href="create_meeting.php"><i class="fas fa-add"></i>Create Meeting</a></li>
                </ul>
            </div>
        </section>
        <section class="dashboard1">
            <div class="container">
                <div class="profile-column">
                <img style="width: 400px;" src="../uploads/<?php echo $meeting['image']; ?>" alt="Meeting Image">
                    <div class="profile-details">                       
                    </div>
                </div>
                <div class="button-list-column">
                    <ul class="icon-list1">
                        <li><?php echo htmlspecialchars($meeting['title']); ?></li>
                        <li><strong>Date:</strong> <?php echo htmlspecialchars($meeting['date']); ?></li>
                        <li>strong>Time:</strong> <?php echo htmlspecialchars($meeting['time']); ?></li>
                        <li><strong>Location:</strong> <?php echo htmlspecialchars($meeting['location']); ?></li>
                        <li> <strong>Description:</strong> <?php echo htmlspecialchars($meeting['description']); ?></li>
                        
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
