<?php
// File: view/student/list_clubs.php

$student_id = getUserId(); 
$message = '';

// XỬ LÝ KHI SINH VIÊN GỬI YÊU CẦU THAM GIA 
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

// LẤY DANH SÁCH TẤT CẢ CLB, KÈM TRẠNG THÁI CỦA SINH VIÊN

$sql_get_clubs = "
    SELECT 
        c.club_id, 
        c.name, 
        c.description,
        cm.status  -- Sẽ là 'approved', 'pending' hoặc NULL
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

<div class="content-box">
    <table>
        <thead>
            <tr>
                <th>Tên CLB</th>
                <th>Mô tả</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clubs as $club): ?>
            <tr>
                <td><?php echo htmlspecialchars($club['name']); ?></td>
                <td><?php echo htmlspecialchars($club['description']); ?></td>
                <td>
                    
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
                    </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>