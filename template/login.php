<?php
// Tệp này được gọi bởi index.php
// Biến $error_message (nếu có) sẽ được truyền từ index.php
?>
<html>
<head>
    <title>Đăng nhập hệ thống</title>
    <link rel="stylesheet" href="assets/css/login.css">

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-box">
        
        <div class="logo-container">
        </div>
        <h1 class="main-title">ĐĂNG NHẬP</h1>
        
        <?php 
        // Hiển thị thông báo lỗi nếu $error_message tồn tại
        if (isset($error_message) && !empty($error_message)): 
        ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form method="POST" action="index.php?action=login">
            
            <label for="username" class="input-label">Tên đăng nhập</label>
            <input type="text" id="username" name="username" required class="custom-input">

            <label for="password" class="input-label">Mật khẩu</label>
            <input type="password" id="password" name="password" required class="custom-input">
            <button type="submit" class="login-button">Đăng nhập</button>
        </form>
        
        <div class="register-link-container">
            Bạn chưa có tài khoản? 
            <a href="index.php?page=register" class="register-link">Tạo tài khoản</a>
        </div>
    </div>
</body>
</html>