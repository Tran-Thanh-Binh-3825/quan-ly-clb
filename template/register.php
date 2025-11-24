<?php
// File: view/register.php
// Biến $error_message (nếu có) sẽ được truyền từ index.php
?>
<html>
<head>
    <title>Đăng ký tài khoản</title>
    <link rel="stylesheet" href="assets/css/register.css">

</head>
<body>
    <div class="register-box">
        <h2>Tạo tài khoản mới</h2>
        
        <?php 
        // Hiển thị lỗi (nếu index.php gửi qua)
        if (isset($error_message) && !empty($error_message)): 
        ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form method="POST" action="index.php?action=register">
            <div>
                <label for="full_name">Họ và tên:</label>
                <input type="text" id="full_name" name="full_name" required>
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="username">Tên đăng nhập:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="password">Mật khẩu:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div>
                <label for="password_confirm">Xác nhận mật khẩu:</label>
                <input type="password" id="password_confirm" name="password_confirm" required>
            </div>
            
            <button type="submit" name="register">Đăng ký</button>
        </form>
        
        <div class="login-link">
            Đã có tài khoản? <a href="index.php">Quay lại đăng nhập</a>
        </div>
    </div>
</body>
</html>