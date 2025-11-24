<link rel="stylesheet" href="assets/css/style.css">
<?php
// File: view/admin/manage_users.php

// XỬ LÝ KHI ADMIN TẠO USER MỚI (POST)
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
    
    $username = $_POST['username'];
    $password_raw = $_POST['password']; 
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $role = $_POST['role']; 

    try {
        $sql = "INSERT INTO users (username, password, full_name, email, role, status) 
                VALUES (?, ?, ?, ?, ?, 'active')";
                
        $stmt = $pdo->prepare($sql);
        
        // Lưu trực tiếp $password_raw vào CSDL
        $stmt->execute([$username, $password_raw, $full_name, $email, $role]);

        $message = '<p class="success">Tạo tài khoản mới thành công!</p>';

    } catch (PDOException $e) {
        // Bắt lỗi nếu trùng username hoặc email 
        if ($e->errorInfo[1] == 1062) {
            $message = '<p class="error">Lỗi: Tên đăng nhập hoặc Email đã tồn tại.</p>';
        } else {
            $message = '<p class="error">Lỗi: ' . $e->getMessage() . '</p>';
        }
    }
}

//  LẤY DANH SÁCH TẤT CẢ USER ĐỂ HIỂN THỊ (GET)
$stmt_users = $pdo->query("SELECT user_id, username, full_name, email, role, status FROM users ORDER BY role, user_id DESC");
$users = $stmt_users->fetchAll();

?>

<h3>Quản lý Người dùng</h3>

<button id="openUserModalBtn" class="btn1">Thêm tài khoản</button>

<div id="userModal" class="modal">
    
    <div class="modal-content">
        
        <div class="modal-header">
            <h4>Tạo Tài khoản Mới</h4>
            <span class="close-btn">&times;</span>
        </div>

        <div class="modal-body">
            <?php if (!empty($message)) echo "<div class='msg-alert'>$message</div>"; ?>

            <form method="POST" action="index.php?page=admin/manage_users">
                
                <div class="form-group">
                    <label>Tên đăng nhập:</label>
                    <input type="text" name="username" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Mật khẩu:</label>
                    <input type="password" name="password" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Tên đầy đủ:</label>
                    <input type="text" name="full_name" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Email:</label>
                    <input type="text" name="email" required class="form-control"> 
                </div>

                <div class="form-group">
                    <label>Vai trò:</label>
                    <select name="role" class="form-control">
                        <option value="student">Sinh viên (Student)</option>
                        <option value="leader">Chủ nhiệm CLB (Leader)</option>
                        <option value="admin">Quản trị viên (Admin)</option>
                    </select>
                </div>
                
                <button type="submit" name="create_user" class="btn-submit">Tạo Người dùng</button>
            </form>
        </div>
    </div>
</div>

<div class="content-box">
    <h4>Danh sách tài khoản (Tổng: <?php echo count($users); ?>)</h4>

    <?php
    if (isset($_GET['update']) && $_GET['update'] == 'success') {
        echo '<p class="success">Cập nhật thông tin người dùng thành công!</p>';
    }
    if (isset($_GET['lock']) && $_GET['lock'] == 'success') {
        echo '<p class="success" style="background-color: #fff9c4; border-color: #ffee58;">Thay đổi trạng thái tài khoản thành công!</p>';
    }
    if (isset($_GET['error'])) {
        echo '<p class="error">' . htmlspecialchars($_GET['error']) . '</p>';
    }
    ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên đăng nhập</th>
                <th>Tên đầy đủ</th>
                <th>Email</th>
                <th>Vai trò</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo $user['user_id']; ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><strong><?php echo htmlspecialchars($user['role']); ?></strong></td>
                <td>
                    <?php if ($user['status'] == 'active'): ?>
                        <span style="color: green; font-weight: bold;">Active</span>
                    <?php else: ?>
                        <span style="color: red; font-weight: bold;">Locked</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="index.php?page=admin/edit_user&user_id=<?php echo $user['user_id']; ?>" 
                       style="text-decoration: none; color: #007bff;">Sửa</a> | 
                    
                    <?php 
                    if ($user['status'] == 'active'): ?>
                        <a href="index.php?action=toggle_lock&user_id=<?php echo $user['user_id']; ?>&status=active" 
                           style="text-decoration: none; color: red;"
                           onclick="return confirm('Bạn có chắc muốn KHÓA tài khoản này?');">Khóa</a>
                    <?php else: ?>
                        <a href="index.php?action=toggle_lock&user_id=<?php echo $user['user_id']; ?>&status=locked" 
                           style="text-decoration: none; color: green;"
                           onclick="return confirm('Bạn có chắc muốn MỞ KHÓA tài khoản này?');">Mở khóa</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="assets/js/script.js"></script>
<script>
    // Chỉ cần 1 dòng này là Modal hoạt động
    setupModal("userModal", "openUserModalBtn");

    // Giữ modal mở nếu PHP báo lỗi
    <?php if (!empty($message)): ?>
        document.getElementById("userModal").style.display = "block";
    <?php endif; ?>
</script>