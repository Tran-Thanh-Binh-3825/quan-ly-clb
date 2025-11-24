<?php
// File: view/student/view_announcements.php

$student_id = getUserId(); // Lấy ID của sinh viên

// LẤY THÔNG BÁO TỪ CÁC CLB MÀ SINH VIÊN ĐÃ THAM GIA ('approved')
$sql_ann = "SELECT 
                a.title, 
                a.content, 
                a.created_at, 
                c.name AS club_name,
                u.full_name AS author_name
            FROM 
                announcements a
            JOIN 
                clubs c ON a.club_id = c.club_id
            JOIN 
                club_members cm ON c.club_id = cm.club_id
            LEFT JOIN
                users u ON a.author_id = u.user_id
            WHERE 
                cm.user_id = ? AND cm.status = 'approved'
            ORDER BY 
                a.created_at DESC"; 

$stmt_ann = $pdo->prepare($sql_ann);
$stmt_ann->execute([$student_id]);
$announcements = $stmt_ann->fetchAll();

?>

<h3>Thông báo từ CLB của bạn</h3>
<p>Các thông báo mới nhất từ những Câu lạc bộ bạn đã tham gia.</p>

<div class="content-box">
    <?php if (empty($announcements)): ?>
        <p style="text-align: center; color: #888;">
            Hiện không có thông báo nào từ các CLB của bạn.
        </p>
    <?php endif; ?>

    <?php foreach ($announcements as $ann): ?>
        
        <div class="announcement-item">
            <h4><?php echo htmlspecialchars($ann['title']); ?></h4>
            <div class="announcement-meta">
                Đăng bởi <strong><?php echo htmlspecialchars($ann['author_name'] ?? 'Quản trị viên'); ?></strong> 
                (CLB: <strong><?php echo htmlspecialchars($ann['club_name']); ?></strong>)
                vào lúc <?php echo date('d-m-Y H:i', strtotime($ann['created_at'])); ?>
            </div>
            <div class="announcement-content">
                <?php echo nl2br(htmlspecialchars($ann['content'])); ?>
            </div>
        </div>
        
    <?php endforeach; ?>
</div>