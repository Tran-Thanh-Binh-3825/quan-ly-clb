<?php
// File: view/admin/edit_user.php

//  Lấy user_id từ URL
if (!isset($_GET['user_id'])) {
    echo "<h3>Lỗi</h3><p class='error'>Không tìm thấy ID người dùng.</p>";
    include 'view/footer.php';
    exit;
}

$user_id_to_edit = $_GET['user_id'];

//  Lấy thông tin user từ CSDL
try {
    $sql_user = "SELECT user_id, username, full_name, email, role FROM users WHERE user_id = ?";
    $stmt_user = $pdo->prepare($sql_user);
    $stmt_user->execute([$user_id_to_edit]);
    $user = $stmt_user->fetch();

    if (!$user) {
        echo "<h3>Lỗi</h3><p class='error'>Người dùng không tồn tại.</p>";
        include 'view/footer.php';
        exit;
    }
} catch (PDOException $e) {
    echo "<h3>Lỗi</h3><p class='error'>Lỗi CSDL: " . $e->getMessage() . "</p>";
    include 'view/footer.php';
    exit;
}

?>

<h3>Chỉnh sửa người dùng: <?php echo htmlspecialchars($user['username']); ?></h3>

<div class="content-box">
    <form method="POST" action="index.php?action=update_user">
    
        <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
        
        <label for="username">Tên đăng nhập:</label>
        <input type="text" id="username" name="username" 
               value="<?php echo htmlspecialchars($user['username']); ?>" required>
        
        <label for="full_name">Tên đầy đủ:</label>
        <input type="text" id="full_name" name="full_name" 
               value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" 
               value="<?php echo htmlspecialchars($user['email']); ?>" required>
        
        <label for="password">Mật khẩu mới:</label>
        <input type="password" id="password" name="password">
        <small style="display: block; margin-bottom: 15px;">
            ** Để trống nếu bạn không muốn thay đổi mật khẩu **
        </small>
        
        <label for="role">Vai trò:</label>
        <select id="role" name="role" style="width: 100%; padding: 8px; margin-bottom: 15px;">
            <option value="student" <?php if($user['role'] == 'student') echo 'selected'; ?>>
                Sinh viên (Student)
            </option>
            <option value="leader" <?php if($user['role'] == 'leader') echo 'selected'; ?>>
                Chủ nhiệm CLB (Leader)
            </option>
            <option value="admin" <?php if($user['role'] == 'admin') echo 'selected'; ?>>
                Quản trị viên (Admin)
            </option>
        </select>
        
        <button type="submit" name="update_user">Cập nhật Người dùng</button>
        <a href="index.php?page=admin/manage_users" style="text-decoration: none; margin-left: 10px; color: #555;">Hủy</a>
    </form>
</div>