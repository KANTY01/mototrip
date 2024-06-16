<?php
session_start();
require_once 'db.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    http_response_code(403);
    exit("403 Forbidden - Unauthorized access");
}

// Validate and sanitize meeting ID
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$meeting_id = isset($data['meeting_id']) ? $data['meeting_id'] : null;

if (!$meeting_id) {
    http_response_code(400);
    exit(json_encode(['success' => false, 'message' => 'Meeting ID is required']));
}

try {
    // Delete the meeting from the database
    $query = "DELETE FROM meetings WHERE id = :meeting_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['meeting_id' => $meeting_id]);

    // Check if the meeting was deleted successfully
    if ($stmt->rowCount() > 0) {
        // Meeting deleted successfully
        $response = ['success' => true];
    } else {
        // Failed to delete meeting
        $response = ['success' => false, 'message' => 'Failed to delete meeting'];
    }
} catch (PDOException $e) {
    // Database error
    $response = ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
