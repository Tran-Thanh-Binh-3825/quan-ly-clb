<link rel="stylesheet" href="assets/css/style.css">
<?php
// File: view/leader/manage_announcements.php

$leader_id = getUserId();
$my_club_id = getLeaderClubId($pdo, $leader_id); // Dùng hàm trợ giúp
$message = '';

//  KIỂM TRA QUYỀN
if (!$my_club_id) {
    echo "<h3>Lỗi Truy cập</h3>";
    echo "<p class='error'>Bạn không được phân quyền làm chủ nhiệm của bất kỳ CLB nào.</p>";
    include 'view/footer.php'; 
    exit;
}

//  HIỂN THỊ THÔNG BÁO (Từ index.php chuyển về)
if (isset($_GET['create']) && $_GET['create'] == 'success') {
    $message = '<p class="success">Đăng thông báo mới thành công!</p>';
}
if (isset($_GET['delete']) && $_GET['delete'] == 'success') {
    $message = '<p class="success" style="background-color: #fff9c4; border-color: #ffee58;">Đã xóa thông báo.</p>';
}
if (isset($_GET['error'])) {
    $message = '<p class"error">' . htmlspecialchars($_GET['error']) . '</p>';
}

//  LẤY DANH SÁCH THÔNG BÁO CŨ CỦA CLB NÀY
$sql_ann = "SELECT * FROM announcements WHERE club_id = ? ORDER BY created_at DESC";
$stmt_ann = $pdo->prepare($sql_ann);
$stmt_ann->execute([$my_club_id]);
$announcements = $stmt_ann->fetchAll();

?>

<h3>Quản lý Thông báo CLB</h3>
<?php echo $message; ?>
<button id="btnCreateAnnounce" class="btn-success" style="margin-bottom: 20px;">
    Đăng thông báo mới
</button>

<div id="modalAnnounce" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Tạo thông báo mới</h4>
            <span class="close-btn">&times;</span>
        </div>

        <div class="modal-body">
            <form method="POST" action="index.php?page=leader/manage_announcements">
                
                <div class="form-group">
                    <label for="title">Tiêu đề thông báo:</label>
                    <input type="text" id="title" name="title" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="content">Nội dung:</label>
                    <textarea id="content" name="content" rows="6" required class="form-control"></textarea>
                </div>
                
                <button type="submit" name="create_announcement" class="btn-success btn-full">Đăng Thông báo</button>
            </form>
        </div>
    </div>
</div>

<div class="content-box">
    <h4>Thông báo đã đăng (<?php echo count($announcements); ?>)</h4>
    <table>
        <thead>
            <tr>
                <th style="width: 25%;">Tiêu đề</th>
                
                <th style="width: 40%;">Nội dung</th> 
                
                <th style="width: 20%;">Ngày đăng</th>
                <th style="width: 15%;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($announcements)): ?>
                <tr><td colspan="4" style="text-align: center;">Bạn chưa đăng thông báo nào.</td></tr>
            <?php endif; ?>
            
            <?php foreach ($announcements as $ann): ?>
            <tr>
                <td><?php echo htmlspecialchars($ann['title']); ?></td>
                
                <td>
                    <?php 
                        $snippet = mb_substr(htmlspecialchars($ann['content']), 0, 80);
                        echo $snippet;
                        if (mb_strlen($ann['content']) > 80) {
                            echo '...';
                        }
                    ?>
                </td>
                
                <td><?php echo date('d-m-Y H:i', strtotime($ann['created_at'])); ?></td>
                <td>
                    <a href="index.php?action=delete_announcement&announcement_id=<?php echo $ann['announcement_id']; ?>" 
                       style="text-decoration: none; color: red;"
                       onclick="return confirm('Bạn có chắc muốn XÓA thông báo này?');">Xóa</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="assets/js/script.js"></script>
<script>
    // 1. Kích hoạt modal
    setupModal("modalAnnounce", "btnCreateAnnounce");
</script>