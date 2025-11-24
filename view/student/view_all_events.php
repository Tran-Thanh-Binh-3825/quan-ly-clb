<link rel="stylesheet" href="assets/css/view_all_events.css">
<?php
// File: view/student/view_all_events.php
$student_id = getUserId(); 

//  LẤY SỰ KIỆN TỪ CÁC CLB MÀ SINH VIÊN ĐÃ THAM GIA ('approved')
$sql_events = "SELECT 
                    e.title, 
                    e.description, 
                    e.start_time, 
                    e.end_time, 
                    e.location, 
                    c.name AS club_name 
                FROM 
                    events e
                JOIN 
                    clubs c ON e.club_id = c.club_id
                JOIN 
                    club_members cm ON c.club_id = cm.club_id
                WHERE 
                    cm.user_id = ? AND cm.status = 'approved'
                ORDER BY 
                    e.start_time DESC"; 

$stmt_events = $pdo->prepare($sql_events);
$stmt_events->execute([$student_id]);
$events = $stmt_events->fetchAll();

?>

<h3>Sự kiện từ CLB của bạn</h3>
<p>Đây là danh sách các sự kiện (sắp tới và đã diễn ra) từ những Câu lạc bộ bạn đã tham gia.</p>

<div class="content-box">
    <?php if (empty($events)): ?>
        <p style="text-align: center; color: #888;">
            Các CLB bạn đã tham gia chưa có sự kiện nào.
            <br><br>
            <a href="index.php?page=student/list_clubs" style="text-decoration: none;">
                <button type="button">Tìm thêm CLB</button>
            </a>
        </p>
    <?php endif; ?>

    <?php foreach ($events as $event): ?>
        
        <div class="event-item">
            <h4><?php echo htmlspecialchars($event['title']); ?></h4>
            <ul>
                <li><strong>CLB tổ chức:</strong> <?php echo htmlspecialchars($event['club_name']); ?></li>
                <li><strong>Thời gian:</strong> <?php echo date('d-m-Y H:i', strtotime($event['start_time'])); ?></li>
                <li><strong>Địa điểm:</strong> <?php echo htmlspecialchars($event['location']); ?></li>
                
                <li style="list-style: none; margin-top: 10px;">
                    <strong>Mô tả:</strong><br>
                    <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                </li>
            </ul>
        </div>
        
    <?php endforeach; ?>
</div>