<link rel="stylesheet" href="assets/css/style.css">
<?php
// File: view/leader/manage_events.php

$leader_id = getUserId();
$my_club_id = getLeaderClubId($pdo, $leader_id); // Dùng hàm mới
$message = '';

//  KIỂM TRA QUYỀN (Nếu không phải leader của CLB nào)
if (!$my_club_id) {
    echo "<h3>Lỗi Truy cập</h3>";
    echo "<p class='error'>Bạn không được phân quyền làm chủ nhiệm của bất kỳ CLB nào.</p>";
    include 'view/footer.php'; 
    exit;
}

//  HIỂN THỊ THÔNG BÁO (Từ index.php chuyển về)
if (isset($_GET['create']) && $_GET['create'] == 'success') {
    $message = '<p class="success">Tạo sự kiện mới thành công!</p>';
}
if (isset($_GET['update']) && $_GET['update'] == 'success') {
    $message = '<p class="success">Cập nhật sự kiện thành công!</p>';
}
if (isset($_GET['delete']) && $_GET['delete'] == 'success') {
    $message = '<p class="success" style="background-color: #fff9c4; border-color: #ffee58;">Đã xóa sự kiện.</p>';
}
if (isset($_GET['error'])) {
    $message = '<p class"error">' . htmlspecialchars($_GET['error']) . '</p>';
}

//  LẤY DANH SÁCH SỰ KIỆN CỦA CLB NÀY
$sql_events = "SELECT * FROM events WHERE club_id = ? ORDER BY start_time DESC";
$stmt_events = $pdo->prepare($sql_events);
$stmt_events->execute([$my_club_id]);
$events = $stmt_events->fetchAll();

?>

<h3>Quản lý Sự kiện/Hoạt động</h3>
<?php echo $message; ?>
<button id="btnCreateEvent" class="btn-success" style="margin-bottom: 20px;">
    + Tạo sự kiện mới
</button>

<div id="modalCreateEvent" class="modal">
    <div class="modal-content"> <div class="modal-header">
            <h4>Tạo sự kiện mới</h4>
            <span class="close-btn">&times;</span> </div>

        <div class="modal-body">
            <form method="POST" action="index.php?action=create_event">
                
                <div class="form-group">
                    <label for="title">Tiêu đề sự kiện:</label>
                    <input type="text" id="title" name="title" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="description">Mô tả:</label>
                    <textarea id="description" name="description" rows="3" class="form-control"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="location">Địa điểm:</label>
                    <input type="text" id="location" name="location" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="start_time">Thời gian bắt đầu:</label>
                    <input type="datetime-local" id="start_time" name="start_time" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="end_time">Thời gian kết thúc:</label>
                    <input type="datetime-local" id="end_time" name="end_time" class="form-control">
                </div>
                
                <button type="submit" name="create_event" class="btn-success btn-full">Tạo Sự kiện</button>
            </form>
        </div>
    </div>
</div>

<div class="content-box">
    <h4>Sự kiện đã tạo (<?php echo count($events); ?>)</h4>
    <table>
        <thead>
            <tr>
                <th>Tiêu đề</th>
                <th>Thời gian bắt đầu</th>
                <th>Địa điểm</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($events)): ?>
                <tr><td colspan="4" style="text-align: center;">Bạn chưa tạo sự kiện nào.</td></tr>
            <?php endif; ?>
            
            <?php foreach ($events as $event): ?>
            <tr>
                <td><?php echo htmlspecialchars($event['title']); ?></td>
                <td><?php echo date('d-m-Y H:i', strtotime($event['start_time'])); ?></td>
                <td><?php echo htmlspecialchars($event['location']); ?></td>
                <td>
                    <a href="index.php?page=leader/edit_event&event_id=<?php echo $event['event_id']; ?>" 
                       style="text-decoration: none; color: #007bff;">Sửa</a> | 
                       
                    <a href="index.php?action=delete_event&event_id=<?php echo $event['event_id']; ?>" 
                       style="text-decoration: none; color: red;"
                       onclick="return confirm('Bạn có chắc muốn XÓA sự kiện này?');">Xóa</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="assets/js/script.js"></script>
<script>
    // Gọi hàm setupModal(ID_Modal, ID_Nút_Mở)
    setupModal("modalCreateEvent", "btnCreateEvent");
</script>