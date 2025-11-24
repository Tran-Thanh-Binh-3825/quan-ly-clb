<?php
// File: view/student/my_clubs.php

$student_id = getUserId(); 
$message = '';
//  HIỂN THỊ THÔNG BÁO RỜI CLB
if (isset($_GET['leave']) && $_GET['leave'] == 'success') {
    $message = '<p class="success" style="background-color: #fff9c4; border-color: #ffee58;">Bạn đã rời CLB thành công.</p>';
}

//  XỬ LÝ KHI SINH VIÊN HỦY YÊU CẦU (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_request'])) {
    
    $member_id = $_POST['member_id']; 
    try {
        $sql_delete = "DELETE FROM club_members 
                       WHERE member_id = ? AND user_id = ? AND status = 'pending'";
        
        $stmt_delete = $pdo->prepare($sql_delete);
        $stmt_delete->execute([$member_id, $student_id]);
        
        $message = '<p class="success" style="background-color: #fff9c4; border-color: #ffee58;">Đã hủy yêu cầu tham gia.</p>';

    } catch (PDOException $e) {
        $message = '<p class"error">Lỗi: ' . $e->getMessage() . '</p>';
    }
}


//  LẤY DANH SÁCH CÁC CLB CỦA SINH VIÊN 
$sql_myclubs = "SELECT 
                    c.name AS club_name, 
                    c.description AS club_desc,
                    cm.status, 
                    cm.joined_at,
                    cm.member_id 
                FROM club_members cm
                JOIN clubs c ON cm.club_id = c.club_id
                WHERE cm.user_id = ?
                ORDER BY cm.status DESC"; 

$stmt_myclubs = $pdo->prepare($sql_myclubs);
$stmt_myclubs->execute([$student_id]);
$my_clubs = $stmt_myclubs->fetchAll();

?>

<h3>Câu lạc bộ của tôi</h3>
<?php echo $message; ?>

<div class="content-box">
    <h4>Danh sách các CLB đã đăng ký</h4>
    <p>Đây là danh sách các CLB bạn đã tham gia hoặc đang chờ duyệt.</p>

    <table>
        <thead>
            <tr>
                <th>Tên Câu lạc bộ</th>
                <th>Trạng thái</th>
                <th>Thông tin/Hành động</th>
            </tr>
        </thead>
        <tbody>

            <?php if (empty($my_clubs)): ?>
            <tr>
                <td colspan="3" style="text-align: center;">
                    Bạn chưa tham gia hoặc gửi yêu cầu tham gia CLB nào.
                    <br><br>
                    <a href="index.php?page=student/list_clubs" style="text-decoration: none;">
                        <button type="button">Xem danh sách CLB ngay</button>
                    </a>
                </td>
            </tr>
            <?php endif; ?>

            <?php foreach ($my_clubs as $club): ?>
            <tr>
                <td>
                    <strong><?php echo htmlspecialchars($club['club_name']); ?></strong>
                    <br>
                    <small><?php echo htmlspecialchars($club['club_desc']); ?></small>
                </td>
                
            <td>
    <?php if ($club['status'] == 'approved'): ?>
        
        <form method="POST" action="index.php?action=leave_club" style="margin: 0;" 
              onsubmit="return confirm('Bạn có chắc chắn muốn rời CLB này?');">
            <input type="hidden" name="member_id" value="<?php echo $club['member_id']; ?>">
            <button type="submit" name="leave_club" style="background-color: #dc3545;">Rời CLB</button>
        </form>
        
    <?php elseif ($club['status'] == 'pending'): ?>
        
        <form method="POST" action="index.php?page=student/my_clubs" style="margin: 0;">
            <input type="hidden" name="member_id" value="<?php echo $club['member_id']; ?>">
            <button type="submit" name="cancel_request" style="background-color: #ffc107; color: #333;">Hủy yêu cầu</button>
        </form>
        
    <?php endif; ?>
</td>

                <td>
                    <?php if ($club['status'] == 'approved'): ?>
                        <span>Ngày tham gia: <?php echo date('d-m-Y', strtotime($club['joined_at'])); ?></span>
                    <?php elseif ($club['status'] == 'pending'): ?>
                        <form method="POST" action="index.php?page=student/my_clubs" style="margin: 0;">
                            <input type="hidden" name="member_id" value="<?php echo $club['member_id']; ?>">
                            <button type="submit" name="cancel_request" style="background-color: #dc3545;">Hủy yêu cầu</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>

        </tbody>
    </table>
</div>