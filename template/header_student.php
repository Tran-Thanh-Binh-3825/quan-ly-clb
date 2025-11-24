<?php
// Tệp này được include bởi index.php, 
// nên nó có thể sử dụng tất cả các hàm từ config.php
// như isLoggedIn(), getRole(), getName()
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống Quản lý CLB</title>
    <link rel="stylesheet" href="assets/css/header_student.css">


</head>
<body>

<nav class="navbar">
    <div class="navbar-links">
        <a href="index.php?page=home">Trang chủ</a>
            <a href="index.php?page=student/list_clubs">Xem Danh sách CLB</a>
            <a href="index.php?page=student/my_clubs">CLB của tôi</a>
            <a href="index.php?page=student/view_all_events">Sự kiện CLB</a>
            <a href="index.php?page=student/view_announcements">Xem Thông báo</a>
    </div>

    <div class="navbar-user">
        <?php if (isLoggedIn()): ?>
            <span>
                Chào, <strong><?php echo htmlspecialchars(getName()); ?></strong>
                (<?php echo htmlspecialchars($role); ?>) |
            </span>
            <a href="index.php?action=logout">Đăng xuất</a>
        <?php endif; ?>
    </div>
</nav>
<img src="uploads/banner.jpg" alt="Welcome Banner" class="container-banner">
