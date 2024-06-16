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
$bio = $_POST['bio'] ?? '';
$profile_picture = $_FILES['profile_picture']['name'] ?? '';

try {
    // Begin transaction
    $pdo->beginTransaction();

    if (!empty($profile_picture)) {
        $profile_picture_tmp = $_FILES['profile_picture']['tmp_name'];
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($profile_picture);

        if (!move_uploaded_file($profile_picture_tmp, $target_file)) {
            throw new Exception('Error uploading file');
        }

        $stmt = $pdo->prepare("UPDATE profiles SET bio = :bio, profile_picture = :profile_picture WHERE user_id = :user_id");
        $stmt->bindParam(':bio', $bio);
        $stmt->bindParam(':profile_picture', $profile_picture);
        $stmt->bindParam(':user_id', $user_id);
        if (!$stmt->execute()) {
            throw new Exception('Error updating profile');
        }
    } else {
        $stmt = $pdo->prepare("UPDATE profiles SET bio = :bio WHERE user_id = :user_id");
        $stmt->bindParam(':bio', $bio);
        $stmt->bindParam(':user_id', $user_id);
        if (!$stmt->execute()) {
            throw new Exception('Error updating profile');
        }

        // Get the current profile picture to return in the response
        $stmt = $pdo->prepare("SELECT profile_picture FROM profiles WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);
        $profile_picture = $profile['profile_picture'] ?? '';
    }

    // Commit transaction
    $pdo->commit();

    echo json_encode(['success' => true, 'profile_picture' => $profile_picture]);
} catch (Exception $e) {
    // Rollback transaction
    $pdo->rollBack();
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
