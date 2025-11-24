<?php
// File: view/leader/edit_event.php

$leader_id = getUserId();
$my_club_id = getLeaderClubId($pdo, $leader_id); // Dùng hàm mới

// 1. Lấy event_id từ URL
if (!isset($_GET['event_id'])) {
    echo "<h3>Lỗi</h3><p class='error'>Không tìm thấy ID sự kiện.</p>";
    include 'view/footer.php';
    exit;
}
$event_id = $_GET['event_id'];

// 2. LẤY THÔNG TIN SỰ KIỆN VÀ KIỂM TRA QUYỀN SỞ HỮU
// Đảm bảo sự kiện này thuộc CLB của leader đang đăng nhập
try {
    $sql_event = "SELECT * FROM events WHERE event_id = ? AND club_id = ?";
    $stmt_event = $pdo->prepare($sql_event);
    $stmt_event->execute([$event_id, $my_club_id]);
    $event = $stmt_event->fetch();

    if (!$event) {
        echo "<h3>Lỗi</h3><p class='error'>Sự kiện không tồn tại hoặc bạn không có quyền sửa.</p>";
        include 'view/footer.php';
        exit;
    }
} catch (PDOException $e) {
    echo "<h3>Lỗi</h3><p class='error'>Lỗi CSDL: " . $e->getMessage() . "</p>";
    include 'view/footer.php';
    exit;
}

// 3. Hiển thị lỗi (nếu có, từ index.php gửi về)
$message = '';
if (isset($_GET['error'])) {
    $message = '<p class="error">' . htmlspecialchars($_GET['error']) . '</p>';
}

// Định dạng lại thời gian cho input datetime-local (Y-m-d\TH:i)
$start_time_formatted = date('Y-m-d\TH:i', strtotime($event['start_time']));
$end_time_formatted = !empty($event['end_time']) ? date('Y-m-d\TH:i', strtotime($event['end_time'])) : '';

?>

<h3>Chỉnh sửa sự kiện: <?php echo htmlspecialchars($event['title']); ?></h3>
<?php echo $message; ?>

<div class="content-box">
    <form method="POST" action="index.php?action=update_event">
    
        <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
        
        <label for="title">Tiêu đề sự kiện:</label>
        <input type="text" id="title" name="title" 
               value="<?php echo htmlspecialchars($event['title']); ?>" required>
        
        <label for="description">Mô tả:</label>
        <textarea id="description" name="description" rows="3"><?php echo htmlspecialchars($event['description']); ?></textarea>
        
        <label for="location">Địa điểm:</label>
        <input type="text" id="location" name="location" 
               value="<?php echo htmlspecialchars($event['location']); ?>">
        
        <label for="start_time">Thời gian bắt đầu:</label>
        <input type="datetime-local" id="start_time" name="start_time" 
               value="<?php echo $start_time_formatted; ?>" required>
        
        <label for="end_time">Thời gian kết thúc:</label>
        <input type="datetime-local" id="end_time" name="end_time"
               value="<?php echo $end_time_formatted; ?>">
        
        <button type="submit" name="update_event">Cập nhật Sự kiện</button>
        <a href="index.php?page=leader/manage_events" style="text-decoration: none; margin-left: 10px; color: #555;">Hủy</a>
    </form>
</div>