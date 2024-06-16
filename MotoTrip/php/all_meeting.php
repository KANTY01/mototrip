<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../login.php");
    exit();
}

// Include the database connection file
include 'db.php';

// Fetch all meetings from the database
$query = "SELECT * FROM meetings ORDER BY id DESC"; // Assuming 'meeting_id' is the primary key
$stmt = $pdo->prepare($query);
$stmt->execute();

// Fetch all meetings
$meetings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Close the database connection (optional as PDO closes on script end)
//$pdo = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MotoTrip - All Meetings</title>
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Add your custom styles here */
    </style>
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
                    <li><a href="meetings.php"><i class="fas fa-users"></i> My Meeting</a></li>
                    <li><a href="create_meeting.php"><i class="fas fa-add"></i> Create Meeting</a></li>
                </ul>
            </div>
        </section>
        <h1 style="text-align: center;">All Meeting</h1>
        <section class="dashboard1">
            <div class="container">
                <div class="cart-items">
                    <?php foreach ($meetings as $meeting): ?>
                        <div class="cart-item" data-id="<?php echo $meeting['id']; ?>">
                            <!-- Adjust the image source path as per your setup -->
                            <img src="../uploads/<?php echo htmlspecialchars($meeting['image']); ?>" alt="Meeting Image">
                            <p><?php echo htmlspecialchars($meeting['description']); ?></p>
                            <button class="edit-btn" onclick="viewMeeting(<?php echo $meeting['id']; ?>)">View</button><br>
                            <?php if ($_SESSION['is_admin']): ?>
                                <button class="delete-btn" onclick="deleteMeeting(<?php echo $meeting['id']; ?>)">Delete</button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($meetings)): ?>
                        <p>No meetings found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 MotoTrip. All rights reserved.</p>
        </div>
    </footer>

    <script>
        function viewMeeting(meetingId) {
            window.location.href = 'view_all_meeting.php?id=' + meetingId;
        }

        function deleteMeeting(meetingId) {
    if (confirm("Are you sure you want to delete this meeting?")) {
        fetch('delete_meeting_admin.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ meeting_id: meetingId })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Remove the meeting item from the DOM
                document.querySelector(`.cart-item[data-id='${meetingId}']`).remove();
                alert('Meeting deleted successfully.');
            } else {
                alert('Error deleting meeting: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

    </script>
</body>
</html>
