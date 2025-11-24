<?php
// File: view/header.php

// 1. Lấy vai trò người dùng
$role = getRole(); 

// 2. Kiểm tra logic
if ($role === 'admin' || $role === 'leader') {
    // Nếu là Admin hoặc Leader -> Dùng giao diện Sidebar mới
    include 'template/header_admin.php';
} else {
    // Nếu là Student hoặc chưa đăng nhập (Khách) -> Dùng giao diện Navbar cũ
    include 'template/header_student.php';
}
?>