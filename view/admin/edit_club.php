<?php
// File: view/admin/edit_club.php
//  Lấy club_id từ URL
if (!isset($_GET['club_id'])) {
    echo "<h3>Lỗi</h3><p class='error'>Không tìm thấy ID Câu lạc bộ.</p>";
    include 'view/footer.php';
    exit;
}

$club_id_to_edit = $_GET['club_id'];

//  Lấy thông tin CLB từ CSDL
try {
    $sql_club = "SELECT club_id, name, description, logo_url FROM clubs WHERE club_id = ?";
    $stmt_club = $pdo->prepare($sql_club);
    $stmt_club->execute([$club_id_to_edit]);
    $club = $stmt_club->fetch();

    if (!$club) {
        echo "<h3>Lỗi</h3><p class='error'>Câu lạc bộ không tồn tại.</p>";
        include 'view/footer.php';
        exit;
    }
} catch (PDOException $e) {
    echo "<h3>Lỗi</h3><p class='error'>Lỗi CSDL: " . $e->getMessage() . "</p>";
    include 'view/footer.php';
    exit;
}

//  Hiển thị lỗi 
$message = '';
if (isset($_GET['error'])) {
    $message = '<p class="error">' . htmlspecialchars($_GET['error']) . '</p>';
}

// LẤY DANH SÁCH THÀNH VIÊN ĐÃ DUYỆT CỦA CLB NÀY
$sql_members = "SELECT 
                    u.full_name, 
                    u.email, 
                    cm.member_id, 
                    cm.joined_at
                FROM 
                    club_members cm
                JOIN 
                    users u ON cm.user_id = u.user_id
                WHERE 
                    cm.club_id = ? AND cm.status = 'approved'
                ORDER BY 
                    cm.joined_at DESC";
$stmt_members = $pdo->prepare($sql_members);
$stmt_members->execute([$club_id_to_edit]);
$members = $stmt_members->fetchAll();

?>

<h3>Chỉnh sửa Câu lạc bộ: <?php echo htmlspecialchars($club['name']); ?></h3>
<?php echo $message; ?>

<div class="content-box">
    <form method="POST" action="index.php?action=update_club" enctype="multipart/form-data">
    
        <input type="hidden" name="club_id" value="<?php echo $club['club_id']; ?>">
        
        <label for="club_name">Tên Câu lạc bộ:</label>
        <input type="text" id="club_name" name="name" 
               value="<?php echo htmlspecialchars($club['name']); ?>" required>
        
        <label for="club_description">Mô tả:</label>
        <textarea id="club_description" name="description" rows="5"><?php echo htmlspecialchars($club['description']); ?></textarea>

        <label>Logo hiện tại:</label>
        <div>
            <?php if ($club['logo_url']): ?>
                <img src="<?php echo htmlspecialchars($club['logo_url']); ?>" alt="Logo" style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px; margin-bottom: 10px;">
                <input type="hidden" name="current_logo" value="<?php echo htmlspecialchars($club['logo_url']); ?>">
            <?php else: ?>
                <p>Chưa có logo.</p>
                <input type="hidden" name="current_logo" value="">
            <?php endif; ?>
        </div>

        <label for="logo">Tải lên logo mới (Bỏ trống nếu không muốn đổi):</label>
        <input type="file" id="logo" name="logo" style="margin-bottom: 15px;">
        
        <button type="submit" name="update_club">Cập nhật CLB</button>
        <a href="index.php?page=admin/manage_all_clubs" style="text-decoration: none; margin-left: 10px; color: #555;">Hủy</a>
    </form>
</div>
<div class="content-box" style="margin-top: 25px;">
    <h4>Quản lý Thành viên (<?php echo count($members); ?>)</h4>
    
    <?php 
    // Hiển thị thông báo khi xóa thành viên thành công
    if (isset($_GET['kick']) && $_GET['kick'] == 'success') {
        echo '<p class="success" style="background-color: #fff9c4; border-color: #ffee58;">Đã xóa thành viên khỏi CLB.</p>';
    }
    ?>

    <table>
        <thead>
            <tr>
                <th>Tên Thành viên</th>
                <th>Email</th>
                <th>Ngày tham gia</th>
                <th style="width: 15%;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($members)): ?>
                <tr><td colspan="4" style="text-align: center;">CLB này chưa có thành viên chính thức nào.</td></tr>
            <?php endif; ?>

            <?php foreach ($members as $member): ?>
            <tr>
                <td><?php echo htmlspecialchars($member['full_name']); ?></td>
                <td><?php echo htmlspecialchars($member['email']); ?></td>
                <td><?php echo date('d-m-Y', strtotime($member['joined_at'])); ?></td>
                <td>
                    <form method="POST" action="index.php?action=kick_member" 
                          onsubmit="return confirm('Bạn có chắc muốn XÓA thành viên này khỏi CLB?');" 
                          style="margin: 0;">
                        
                        <input type="hidden" name="member_id" value="<?php echo $member['member_id']; ?>">
                        
                        <input type="hidden" name="club_id" value="<?php echo $club_id_to_edit; ?>"> 
                        
                        <button type="submit" name="kick_member" style="background-color: #dc3545;">Xóa khỏi CLB</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>