<?php
// File: pages/home.php
?>

<?php
// File: view/student/list_clubs.php

$student_id = getUserId(); 
$message = '';

//  XỬ LÝ KHI SINH VIÊN GỬI YÊU CẦU 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['join_club'])) {
    $club_id = $_POST['club_id'];
    
    try {
        $sql = "INSERT INTO club_members (user_id, club_id, status) VALUES (?, ?, 'pending')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$student_id, $club_id]);
        $message = '<p class="success">Gửi yêu cầu thành công! Vui lòng chờ chủ nhiệm CLB duyệt.</p>';
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) {
            $message = '<p class="error">Bạn đã gửi yêu cầu tham gia CLB này rồi.</p>';
        } else {
            $message = '<p class="error">Lỗi: ' . $e->getMessage() . '</p>';
        }
    }
}

// LẤY DANH SÁCH CLB, KÈM TRẠNG THÁI VÀ LOGO
$sql_get_clubs = "
    SELECT 
        c.club_id, 
        c.name, 
        c.description,
        c.logo_url,  -- Thêm logo
        cm.status
    FROM 
        clubs c
    LEFT JOIN 
        club_members cm ON c.club_id = cm.club_id AND cm.user_id = ?
    ORDER BY
        c.name ASC
";
                
$stmt_clubs = $pdo->prepare($sql_get_clubs);
$stmt_clubs->execute([$student_id]);
$clubs = $stmt_clubs->fetchAll();

?>

<h3>Danh sách Câu lạc bộ</h3>
<?php echo $message;  ?>

<div class="club-grid">

    <?php if (empty($clubs)): ?>
        <p>Hiện tại chưa có câu lạc bộ nào.</p>
    <?php endif; ?>

    <?php foreach ($clubs as $club): ?>
    
        <div class="club-card">
        
            <div class="club-card-image">
                <?php if ($club['logo_url']):  ?>
                    <img src="<?php echo htmlspecialchars($club['logo_url']); ?>" alt="<?php echo htmlspecialchars($club['name']); ?>">
                <?php else:  ?>
                    <?php endif; ?>
            </div>
            
            <div class="club-card-content">
                <h4 class="club-card-title"><?php echo htmlspecialchars($club['name']); ?></h4>
                <p class="club-card-desc"><?php echo htmlspecialchars($club['description']); ?></p>
            </div>
            
            <div class="club-card-action">
                <?php if ($club['status'] == 'approved'): ?>
                    <button type="button" disabled style="background-color: #28a745;">
                        Đã tham gia
                    </button>
                
                <?php elseif ($club['status'] == 'pending'): ?>
                    <button type="button" disabled style="background-color: #ffc107; color: #333;">
                        Đang chờ duyệt
                    </button>
                
                <?php else:  ?>
                    <form method="POST" action="index.php?page=student/list_clubs" style="margin: 0;">
                        <input type="hidden" name="club_id" value="<?php echo $club['club_id']; ?>">
                        <button type="submit" name="join_club">Gửi yêu cầu tham gia</button>
                    </form>
                <?php endif; ?>
            </div>
            
        </div>
    <?php endforeach; ?>
    </div>