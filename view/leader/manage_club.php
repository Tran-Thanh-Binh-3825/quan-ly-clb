<link rel="stylesheet" href="assets/css/style.css">
<?php
// File: view/leader/manage_club.php

$leader_id = getUserId();
$message = '';

// TÌM CLB CỦA CHỦ NHIỆM NÀY
$sql_my_club = "SELECT * FROM clubs WHERE leader_id = ?";
$stmt_my_club = $pdo->prepare($sql_my_club);
$stmt_my_club->execute([$leader_id]);
$my_club = $stmt_my_club->fetch();

//  KIỂM TRA NẾU CHƯA LÀ CHỦ NHIỆM
if (!$my_club) {
    echo "<h3>Lỗi Truy cập</h3>";
    echo "<p class='error'>Bạn không được phân quyền làm chủ nhiệm của bất kỳ CLB nào.</p>";
    include 'view/footer.php'; 
    exit;
}

$club_id = $my_club['club_id']; 

//  XỬ LÝ KHI CHỦ NHIỆM CẬP NHẬT THÔNG TIN (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_club_info'])) {
    
    $new_name = $_POST['club_name'];
    $new_description = $_POST['club_description'];
    
    try {
        $sql_update = "UPDATE clubs SET name = ?, description = ? 
                       WHERE club_id = ? AND leader_id = ?";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$new_name, $new_description, $club_id, $leader_id]);
        
        $message = '<p class="success">Cập nhật thông tin CLB thành công!</p>';
        $my_club['name'] = $new_name;
        $my_club['description'] = $new_description;

    } catch (PDOException $e) {
        $message = '<p class="error">Lỗi: ' . $e->getMessage() . '</p>';
    }
}

//  LẤY DANH SÁCH THÀNH VIÊN ĐÃ DUYỆT 
$sql_approved = "SELECT users.full_name, users.email, club_members.joined_at, club_members.member_id 
                 FROM club_members
                 JOIN users ON club_members.user_id = users.user_id
                 WHERE club_members.club_id = ? AND club_members.status = 'approved'
                 ORDER BY club_members.joined_at DESC";

$stmt_approved = $pdo->prepare($sql_approved);
$stmt_approved->execute([$club_id]);
$approved_list = $stmt_approved->fetchAll();

?>

<h3>Quản lý Câu lạc bộ: <?php echo htmlspecialchars($my_club['name']); ?></h3>
<?php echo $message; ?>


<button id="openEditBtn" class="btn-primary" style="margin-bottom: 20px;">
    Cập nhật thông tin CLB
</button>

<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Cập nhật Thông tin</h4>
            <span class="close-btn">&times;</span>
        </div>

        <div class="modal-body">
            <form method="POST" action="index.php?page=leader/manage_club">
                
                <div class="form-group">
                    <label for="club_name">Tên Câu lạc bộ:</label>
                    <input type="text" id="club_name" name="club_name" class="form-control"
                           value="<?php echo htmlspecialchars($my_club['name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="club_description">Mô tả:</label>
                    <textarea id="club_description" name="club_description" rows="5" class="form-control"><?php echo htmlspecialchars($my_club['description']); ?></textarea>
                </div>
                
                <button type="submit" name="update_club_info" class="btn-primary btn-full">Lưu thay đổi</button>
            </form>
        </div>
    </div>
</div>

<div class="content-box" style="margin-top: 25px;">
    <h4>Thành viên chính thức (<?php echo count($approved_list); ?>)</h4>

    <?php 
    if (isset($_GET['kick']) && $_GET['kick'] == 'success') {
        echo '<p class="success" style="background-color: #fff9c4; border-color: #ffee58;">Đã xóa thành viên khỏi CLB.</p>';
    }
    ?>

    <table>
        <thead>
            <tr>
                <th>Tên Sinh viên</th>
                <th>Email</th>
                <th>Ngày tham gia</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($approved_list)): ?>
                <tr>
                    <td colspan="4" style="text-align: center;">Chưa có thành viên chính thức nào.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($approved_list as $member): ?>
            <tr>
                <td><?php echo htmlspecialchars($member['full_name']); ?></td>
                <td><?php echo htmlspecialchars($member['email']); ?></td>
                <td><?php echo date('d-m-Y H:i', strtotime($member['joined_at'])); ?></td>
                
                <td>
                    <form method="POST" action="index.php?action=leader_kick_member" 
                          onsubmit="return confirm('Bạn có chắc muốn XÓA thành viên này?');" 
                          style="margin: 0;">
                        
                        <input type="hidden" name="member_id" value="<?php echo $member['member_id']; ?>">
                        
                        <button type="submit" name="kick" style="background-color: #dc3545;">Xóa</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="assets/js/script.js"></script>
<script>
    // Kích hoạt Modal
    setupModal("editModal", "openEditBtn");
</script>