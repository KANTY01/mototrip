<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

include 'db.php';

$user_id = $_SESSION['user_id'];
$meeting_date = $_POST['meeting_date'] ?? '';
$location = $_POST['location'] ?? '';
$description = $_POST['description'] ?? '';

try {
    $stmt = $pdo->prepare("INSERT INTO meetings (user_id, meeting_date, location, description) VALUES (:user_id, :meeting_date, :location, :description)");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':meeting_date', $meeting_date);
    $stmt->bindParam(':location', $location);
    $stmt->bindParam(':description', $description);

    if ($stmt->execute()) {
        $meeting_id = $pdo->lastInsertId();
        echo json_encode(['success' => true, 'meeting' => [
            'id' => $meeting_id,
            'meeting_date' => $meeting_date,
            'location' => $location,
            'description' => $description
        ]]);
    } else {
        throw new Exception('Error adding meeting');
    }
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
