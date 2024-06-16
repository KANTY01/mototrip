// php/permissions.php
<?php
require 'db.php';
require 'session.php';

function has_permission($user_id, $permission) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT role_name FROM roles r
                           JOIN user_roles ur ON r.id = ur.role_id
                           WHERE ur.user_id = :user_id');
    $stmt->execute(['user_id' => $user_id]);
    $roles = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Define your permissions logic here
    // Example: Check if user has admin role
    return in_array($permission, $roles);
}
?>
