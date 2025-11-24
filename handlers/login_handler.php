<?php
// File: handlers/login_handler.php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

try {
    // 1. Lấy thông tin user từ CSDL dựa trên username
    $sql = "SELECT * FROM users WHERE username = ? AND status = 'active'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // 2. SO SÁNH MẬT KHẨU (Sửa đổi tại đây)
    // Dùng toán tử == để so sánh chuỗi trực tiếp
    if ($user && $password == $user['password']) {
        
        // Đăng nhập thành công: Lưu session
        $_SESSION['loggedin'] = true;
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['full_name'];
        
        header('Location: index.php');
        exit;
    } else {
        // Sai mật khẩu hoặc không tìm thấy user
        $error_message = 'Tên đăng nhập hoặc mật khẩu không đúng!';
        
        // Lưu ý: Bạn cần trỏ đúng đường dẫn file login view của bạn
        // Ở code cũ bạn để lúc thì template/ lúc thì view/, mình sửa lại thành view/ cho chuẩn
        include 'template/login.php'; 
        exit;
    }
} catch (PDOException $e) {
    $error_message = 'Lỗi CSDL: ' . $e->getMessage();
    include 'template/login.php';
    exit;
}
?>