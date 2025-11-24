<?php
// File: pages/leader/approve_members.php

$leader_id = getUserId(); // Lấy ID của chủ nhiệm đang đăng nhập
$message = '';

//  TÌM CLB CỦA CHỦ NHIỆM NÀY
$sql_my_club = "SELECT club_id, name FROM clubs WHERE leader_id = ?";
$stmt_my_club = $pdo->prepare($sql_my_club);
$stmt_my_club->execute([$leader_id]);
$my_club = $stmt_my_club->fetch();

if (!$my_club) {
    echo "<h3>Bạn chưa được phân quyền làm chủ nhiệm CLB nào.</h3>";
    include 'templates/footer.php';
    exit;
}

$club_id = $my_club['club_id'];
$club_name = $my_club['name'];


//  XỬ LÝ KHI CHỦ NHIỆM DUYỆT/TỪ CHỐI
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_POST['member_id'];
    
    if (isset($_POST['approve'])) {
        $sql = "UPDATE club_members SET status = 'approved', joined_at = NOW() WHERE member_id = ? AND club_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$member_id, $club_id]);
        $message = '<p style="color: green;">Duyệt thành viên thành công!</p>';
    }
        if (isset($_POST['reject'])) {
        $sql = "DELETE FROM club_members WHERE member_id = ? AND club_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$member_id, $club_id]);
        $message = '<p style="color: orange;">Đã từ chối/xóa yêu cầu.</p>';
    }
}


//  LẤY DANH SÁCH SINH VIÊN ĐANG CHỜ DUYỆT (pending)
$sql_pending = "SELECT users.full_name, users.email, club_members.member_id 
                FROM club_members
                JOIN users ON club_members.user_id = users.user_id
                WHERE club_members.club_id = ? AND club_members.status = 'pending'";
                
$stmt_pending = $pdo->prepare($sql_pending);
$stmt_pending->execute([$club_id]);
$pending_list = $stmt_pending->fetchAll();

?>

<h3>Duyệt thành viên cho CLB: <?php echo htmlspecialchars($club_name); ?></h3>
<?php echo $message; ?>

<div class="content-box">
    <h4>Danh sách chờ duyệt</h4>
    <table border="1" width="100%">
        <thead>
            <tr>
                <th>Tên Sinh viên</th>
                <th>Email</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pending_list as $sv): ?>
            <tr>
                <td><?php echo htmlspecialchars($sv['full_name']); ?></td>
                <td><?php echo htmlspecialchars($sv['email']); ?></td>
                <td>
                    <form method="POST" action="index.php?page=leader/approve_members" style="display: inline;">
                        <input type="hidden" name="member_id" value="<?php echo $sv['member_id']; ?>">
                        <button type="submit" name="approve" style="color: black;">Duyệt</Tton>
                    </form>
                    <form method="POST" action="index.php?page=leader/approve_members" style="display: inline;">
                        <input type="hidden" name="member_id" value="<?php echo $sv['member_id']; ?>">
                        <button type="submit" name="reject" style="color: black;">Từ chối</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($pending_list)): ?>
            <tr><td colspan="3">Không có yêu cầu nào đang chờ.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>