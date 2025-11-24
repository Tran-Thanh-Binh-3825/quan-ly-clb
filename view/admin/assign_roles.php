<?php
// File: view/admin/assign_roles.php

//  LẤY DỮ LIỆU ĐỂ HIỂN THỊ (GET)
$stmt_leaders = $pdo->query("SELECT user_id, full_name FROM users WHERE role = 'leader' AND status = 'active'");
$leaders_list = $stmt_leaders->fetchAll();

$sql_clubs = "SELECT c.club_id, c.name, c.leader_id, u.full_name AS leader_name
              FROM clubs c
              LEFT JOIN users u ON c.leader_id = u.user_id";
$stmt_clubs = $pdo->query($sql_clubs);
$clubs = $stmt_clubs->fetchAll();

?>

<h3>Phân quyền (Gán Chủ nhiệm CLB)</h3>

<?php 
//  THÊM PHẦN HIỂN THỊ THÔNG BÁO 
if (isset($_GET['update']) && $_GET['update'] == 'success') {
    echo '<p class="success">Cập nhật chủ nhiệm cho CLB thành công!</p>';
}
if (isset($_GET['error'])) {
    echo '<p class="error">' . htmlspecialchars($_GET['error']) . '</p>';
}
?>

<div class="content-box">
    <h4>Danh sách CLB và Chủ nhiệm</h4>
    <p>Chọn một người dùng (có vai trò 'Leader') từ menu thả xuống để gán họ làm chủ nhiệm cho CLB tương ứng.</p>

    <table>
        <thead>
            <tr>
                <th>Tên CLB</th>
                <th>Chủ nhiệm hiện tại</th>
                <th>Gán/Thay đổi Chủ nhiệm</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clubs as $club): ?>
            <tr>
                <form method="POST" action="index.php?action=assign_leader">
                    
                    <input type="hidden" name="club_id" value="<?php echo $club['club_id']; ?>">
                    
                    <td>
                        <strong><?php echo htmlspecialchars($club['name']); ?></strong>
                    </td>
                    
                    <td>
                        <?php 
                        if ($club['leader_name']) {
                            echo htmlspecialchars($club['leader_name']) . ' (ID: ' . $club['leader_id'] . ')';
                        } else {
                            echo '<span style="color: #888;">-- Chưa có --</span>';
                        }
                        ?>
                    </td>

                    <td>
                        <select name="leader_id" style="width: 100%; padding: 8px;">
                            <option value="null">-- Bỏ gán / Chưa có --</option>
                            
                            <?php foreach ($leaders_list as $leader): ?>
                                <option value="<?php echo $leader['user_id']; ?>" 
                                    <?php 
                                    if ($leader['user_id'] == $club['leader_id']) {
                                        echo 'selected';
                                    } 
                                    ?>
                                >
                                    <?php echo htmlspecialchars($leader['full_name']); ?> (ID: <?php echo $leader['user_id']; ?>)
                                </option>
                            <?php endforeach; ?>
                            
                            <?php if (empty($leaders_list)): ?>
                                <option value="" disabled>Không có user nào là 'Leader'</option>
                            <?php endif; ?>
                        </select>
                    </td>

                    <td>
                        <button type="submit" name="assign_leader">Cập nhật</button>
                    </td>
                </form>
                </tr>
            <?php endforeach; ?>
            
            <?php if (empty($clubs)): ?>
            <tr><td colspan="4">Chưa có CLB nào. Bạn cần tạo CLB ở trang "Quản lý CLB" trước.</td></tr>
            <?php endif; ?>

        </tbody>
    </table>
</div>