<?php
//  NẠP CÁC TỆP CẦN THIẾT 
require_once 'config.php'; 
require_once 'functions/auth_helpers.php'; 
require_once 'functions/db_helpers.php'; 

//  ĐỊNH NGHĨA BIẾN TOÀN CỤC 
$action = $_GET['action'] ?? null;
$page = $_GET['page'] ?? 'home'; 
$role = getRole(); 

// PHẦN 1: XỬ LÝ HÀNH ĐỘNG 
if ($action) {
    switch ($action) {
        // Auth
        case 'login':
            require 'handlers/login_handler.php';
            break;
        case 'logout':
            require 'handlers/logout_handler.php';
            break;
        case 'register':
            require 'handlers/register_handler.php';
            break;

        // Student
        case 'leave_club':
            require 'handlers/Student Handlers/student_leave_club.php';
            break;

        // Admin
        case 'toggle_lock':
            require 'handlers/Admin Handlers/admin_toggle_lock.php';
            break;
        case 'update_user':
            require 'handlers/Admin Handlers/admin_update_user.php';
            break;
        case 'update_club':
            require 'handlers/Admin Handlers/admin_update_club.php';
            break;
        case 'delete_club':
            require 'handlers/Admin Handlers/admin_delete_club.php';
            break;
        case 'kick_member':
            require 'handlers/Admin Handlers/admin_kick_member.php';
            break;
            
        // Leader
        case 'leader_kick_member':
            require 'handlers/Leader Handlers/leader_kick_member.php';
            break;
        case 'create_event':
            require 'handlers/Leader Handlers/leader_create_event.php';
            break;
        case 'update_event':
            require 'handlers/Leader Handlers/leader_update_event.php';
            break;
        case 'delete_event':
            require 'handlers/Leader Handlers/leader_delete_event.php';
            break;
        case 'create_announcement':
            require 'handlers/Leader Handlers/leader_create_announcement.php';
            break;
        case 'delete_announcement':
            require 'handlers/Leader Handlers/leader_delete_announcement.php';
            break;
            
        default:
            break;
    }
}

//  KIỂM TRA ĐĂNG NHẬP 
if (!isLoggedIn()) {
    
    if ($page === 'register') {
        include 'template/register.php'; 
        exit;
    }
    
    $error_message = '';   
    $success_message = ''; 
    
    if (isset($_GET['register']) && $_GET['register'] === 'success') {
        $success_message = 'Đăng ký thành công! Vui lòng đăng nhập.';
    }
    
    include 'template/login.php'; 
    exit;
}

// HIỂN THỊ TRANG 
include 'template/header.php'; 

$page_file = 'view/' . $page . '.php'; 

// (Chuyển hướng trang home theo vai trò)
if ($page === 'home') {
    if ($role === 'student') { 
        $page_file = 'view/home.php';
    } elseif ($role === 'leader') { 
        $page_file = 'view/dashboard.php';
    } elseif ($role === 'admin') { 
        $page_file = 'view/dashboard.php';
    } else {
        $page_file = 'view/home.php';
    }
}

// (Bảo mật và Phân quyền 
if (strpos($page, '..') !== false) {
    echo '<h3 class="error">Truy cập không hợp lệ!</h3>';
    $page_file = 'view/home.php'; 
}

if ($role === 'student' && (strpos($page, 'admin/') === 0 || strpos($page, 'leader/') === 0)) {
    echo '<h3 class="error">Bạn không có quyền truy cập trang này!</h3>';
    $page_file = 'view/student/list_clubs.php';
}
elseif ($role === 'leader' && strpos($page, 'admin/') === 0) { 
    echo '<h3 class="error">Bạn không có quyền truy cập trang này!</h3>';
    $page_file = 'view/leader/manage_club.php';
}

if (file_exists($page_file)) {
    include $page_file;
} else {
    echo '<h3>Lỗi 404: Trang không tồn tại.</h3>';
    include 'view/home.php'; 
}
if ($role === 'student') {
    include 'template/footer.php'; 
}

?>