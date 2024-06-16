<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Include the database connection file
include 'db.php';

// Get the data from the request
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['meeting_id'])) {
    $meeting_id = $data['meeting_id'];
    $user_id = $_SESSION['user_id'];

    // Prepare the delete statement
    $query = "DELETE FROM meetings WHERE id = :meeting_id AND user_id = :user_id";
    $stmt = $pdo->prepare($query);

    if ($stmt->execute(['meeting_id' => $meeting_id, 'user_id' => $user_id])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting meeting.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid meeting ID.']);
}
?>
