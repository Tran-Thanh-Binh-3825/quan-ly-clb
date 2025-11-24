<?php
// File: handlers/admin_toggle_lock.php
if ($role !== 'admin') {
    header('Location: index.php');
    exit;
}

$user_id = $_GET['user_id'] ?? 0;
$current_status = $_GET['status'] ?? '';

if ($user_id == getUserId()) {
    header('Location: index.php?page=admin/manage_users&error=Bạn không thể tự khóa chính mình!');
    exit;
}

$new_status = ($current_status == 'active') ? 'locked' : 'active';

try {
    $sql_lock = "UPDATE users SET status = ? WHERE user_id = ?";
    $stmt_lock = $pdo->prepare($sql_lock);
    $stmt_lock->execute([$new_status, $user_id]);
    header('Location: index.php?page=admin/manage_users&lock=success');
    exit;
} catch (PDOException $e) {
    header('Location: index.php?page=admin/manage_users&error=' . $e->getMessage());
    exit;
}
?>