<?php
// File: handlers/register_handler.php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$full_name = $_POST['full_name'] ?? '';
$email = $_POST['email'] ?? '';
$username = $_POST['username'] ?? '';
$password_raw = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';

// 1. Kiểm tra dữ liệu nhập
if (empty($full_name) || empty($email) || empty($username) || empty($password_raw)) {
    $error_message = 'Vui lòng điền đầy đủ thông tin.';
    include 'template/register.php';
    exit;
}

if ($password_raw !== $password_confirm) {
    $error_message = 'Mật khẩu xác nhận không khớp!';
    include 'template/register.php';
    exit;
}

// --- THAY ĐỔI Ở ĐÂY ---
// Bỏ dòng mã hóa password_hash đi
// $password_hashed = password_hash($password_raw, PASSWORD_BCRYPT);

try {
    $sql = "INSERT INTO users (username, password, full_name, email, role, status) 
            VALUES (?, ?, ?, ?, 'student', 'active')";
    $stmt = $pdo->prepare($sql);
    
    // Lưu trực tiếp $password_raw vào cơ sở dữ liệu
    $stmt->execute([$username, $password_raw, $full_name, $email]);
    
    header('Location: index.php?register=success');
    exit;

} catch (PDOException $e) {
    // Bắt lỗi trùng lặp username hoặc email
    if ($e->errorInfo[1] == 1062) {
        $error_message = 'Tên đăng nhập hoặc Email đã tồn tại.';
    } else {
        $error_message = 'Lỗi CSDL: ' . $e->getMessage();
    }
    include 'template/register.php';
    exit;
}
?>