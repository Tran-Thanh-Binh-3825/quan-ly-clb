<?php
// File: handlers/admin_update_user.php
if ($role !== 'admin' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$user_id = $_POST['user_id'];
$username = $_POST['username'];
$full_name = $_POST['full_name'];
$email = $_POST['email'];
$role_form = $_POST['role'];
$password_raw = $_POST['password'];

try {
    if (!empty($password_raw)) {
        $password_hashed = password_hash($password_raw, PASSWORD_BCRYPT);
        $sql_update = "UPDATE users SET username = ?, full_name = ?, email = ?, role = ?, password = ? WHERE user_id = ?";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$username, $full_name, $email, $role_form, $password_hashed, $user_id]);
    } else {
        $sql_update = "UPDATE users SET username = ?, full_name = ?, email = ?, role = ? WHERE user_id = ?";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$username, $full_name, $email, $role_form, $user_id]);
    }
    header('Location: index.php?page=admin/manage_users&update=success');
    exit;
} catch (PDOException $e) {
     if ($e->errorInfo[1] == 1062) {
        $error = 'Lỗi: Tên đăng nhập hoặc Email đã tồn tại.';
    } else {
        $error = 'Lỗi CSDL: ' . $e->getMessage();
    }
    header('Location: index.php?page=admin/edit_user&user_id=' . $user_id . '&error=' . urlencode($error));
    exit;
}
?>