<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống Quản lý CLB</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/header_admin.css">
</head>
<body>

    <nav class="sidebar">
        <div class="sidebar-brand">
            <a href="index.php?page=home">QL CLB</a>
        </div>

        <div class="sidebar-menu">
            <a href="index.php?page=home">🏠 Trang chủ</a>

            <?php $role = getRole(); ?>
        
            <?php if ($role === 'leader'): ?>
                <div class="menu-label">Quản lý CLB</div>
                <a href="index.php?page=leader/manage_club">⚙️ Thông tin chung</a>
                <a href="index.php?page=leader/approve_members">👥 Duyệt thành viên</a>
                <a href="index.php?page=leader/manage_events">📅 Quản lý Sự kiện</a>
                <a href="index.php?page=leader/manage_announcements">📢 Đăng thông báo</a>
                <?php if (isLoggedIn()): ?>
                    <a href="index.php?action=logout" class="btn-logout">Đăng xuất</a>
                <?php else: ?>
                    <a href="index.php?page=login">Đăng nhập</a>
                <?php endif; ?>

            <?php elseif ($role === 'admin'): ?>
                <div class="menu-label">Quản trị hệ thống</div>
                <a href="index.php?page=admin/manage_all_clubs">🏢 Quản lý CLB</a>
                <a href="index.php?page=admin/manage_users">👤 Quản lý User</a>
                <a href="index.php?page=admin/assign_roles">🔑 Phân quyền</a>
                 <?php if (isLoggedIn()): ?>
                    <a href="index.php?action=logout" class="btn-logout">Đăng xuất</a>
                <?php else: ?>
                    <a href="index.php?page=login">Đăng nhập</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </nav>

    <div class="main-wrapper">
        
        <div class="top-header">
            <div class="user-info">
                <?php if (isLoggedIn()): ?>
    Xin chào, <strong><?php echo htmlspecialchars(getName()); ?></strong> 
    (<?php echo htmlspecialchars($role); ?>)
<?php endif; ?>

            </div>
        </div>

        <div class="page-content">