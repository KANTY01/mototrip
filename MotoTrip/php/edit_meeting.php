<?php
    session_start();
    require_once 'db.php';

    // Redirect to login page if user is not logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit();
    }

    // Get the meeting ID from the URL
    $meeting_id = isset($_GET['id']) ? $_GET['id'] : 0;

    // Fetch the meeting details from the database
    $query = "SELECT * FROM meetings WHERE id = :meeting_id AND user_id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['meeting_id' => $meeting_id, 'user_id' => $_SESSION['user_id']]);
    $meeting = $stmt->fetch(PDO::FETCH_ASSOC);

    // Redirect if meeting not found
    if (!$meeting) {
        header("Location: meetings.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Meeting - MotoTrip</title>
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
                    <li><a href="meetings.php"><i class="fas fa-users"></i> Meeting</a></li>
                </ul>
            </div>
        </section>
        <section class="create-meeting">
            <div class="container">
                <h1>Edit Meeting</h1>
                <div class="form-container">
                    <form action="update_meeting.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="meeting_id" value="<?php echo $meeting['id']; ?>">

                        <label for="title">Meeting Title</label>
                        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($meeting['title']); ?>" required>

                        <label for="date">Date</label>
                        <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($meeting['date']); ?>" required>

                        <label for="time">Time</label>
                        <input type="time" id="time" name="time" value="<?php echo htmlspecialchars($meeting['time']); ?>" required>

                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($meeting['location']); ?>" required>

                        <label for="description">Description</label>
                        <textarea id="description" name="description" required><?php echo htmlspecialchars($meeting['description']); ?></textarea>

                        <label for="image">Meeting Image</label>
                        <input type="file" id="image" name="image" accept="image/*">

                        <button type="submit">Update Meeting</button>
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
