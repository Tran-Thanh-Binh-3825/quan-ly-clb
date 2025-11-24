<link rel="stylesheet" href="assets/css/style.css">
<?php
// File: view/admin/manage_all_clubs.php

$message = '';
//  XỬ LÝ KHI ADMIN TẠO CLB MỚI (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_club'])) {
    
    $name = $_POST['club_name'];
    $desc = $_POST['club_desc'];
    $logo_path = NULL; 

    // --- BẮT ĐẦU LOGIC UPLOAD ẢNH ---
    // Kiểm tra xem người dùng có tải tệp lên không
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        
        $target_dir = "uploads/"; 
        $file_extension = strtolower(pathinfo($_FILES["logo"]["name"], PATHINFO_EXTENSION));
        $safe_filename = uniqid() . '_' . time() . '.' . $file_extension;
        $target_file = $target_dir . $safe_filename;
        
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        
        // Kiểm tra định dạng
        if (in_array($file_extension, $allowed_types)) {
            // Di chuyển tệp từ tạm (tmp_name) vào thư mục uploads/
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $target_file)) {
                $logo_path = $target_file; 
            } else {
                $message = '<p class="error">Lỗi: Không thể di chuyển tệp đã tải lên.</p>';
            }
        } else {
            $message = '<p class="error">Lỗi: Chỉ chấp nhận các tệp ảnh (JPG, JPEG, PNG, GIF).</p>';
        }
    }

    if (empty($message)) {
        try {
            // Thêm logo_url vào CSDL
            $sql = "INSERT INTO clubs (name, description, logo_url) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $desc, $logo_path]);
            $message = '<p class="success">Tạo CLB mới thành công!</p>';
        } catch (PDOException $e) {
            $message = '<p class"error">Lỗi CSDL: ' . $e->getMessage() . '</p>';
        }
    }
}

// PHÂN TRANG
$limit = 10; // số CLB mỗi trang
$page = isset($_GET['p']) ? intval($_GET['p']) : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Lấy tổng số CLB
$total_stmt = $pdo->query("SELECT COUNT(*) FROM clubs");
$total_clubs = $total_stmt->fetchColumn();
$total_pages = ceil($total_clubs / $limit);


//  LẤY DANH SÁCH CLB 
$sql_get_clubs = "
    SELECT 
        c.club_id, c.name, c.description, c.logo_url,
        u.full_name AS leader_name,
        SUM(CASE WHEN cm.status = 'approved' THEN 1 ELSE 0 END) AS member_count
    FROM 
        clubs c
    LEFT JOIN 
        users u ON c.leader_id = u.user_id
    LEFT JOIN 
        club_members cm ON c.club_id = cm.club_id
    GROUP BY 
        c.club_id, c.name, c.description, c.logo_url, u.full_name
    ORDER BY 
        c.club_id ASC
    LIMIT :limit OFFSET :offset
";

$stmt_clubs = $pdo->prepare($sql_get_clubs);
$stmt_clubs->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt_clubs->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt_clubs->execute();
$clubs = $stmt_clubs->fetchAll();
?>

<h3>Quản lý Câu lạc bộ</h3>

<button id="openModalBtn" class="btn1">Thêm CLB</button>

<div id="clubModal" class="modal">
    
    <div class="modal-content">
        <div class="modal-header">
            <h4>Tạo CLB Mới</h4>
            <span class="close-btn">&times;</span> </div>

        <div class="modal-body">
            <?php if (!empty($message)) echo "<div class='msg'>$message</div>"; ?>

            <form method="POST" action="index.php?page=admin/manage_all_clubs" enctype="multipart/form-data">
                
                <label for="club_name">Tên CLB:</label>
                <input type="text" id="club_name" name="club_name" required class="form-control">
                
                <label for="club_desc">Mô tả:</label>
                <textarea id="club_desc" name="club_desc" rows="3" class="form-control"></textarea>
                
                <label for="logo">Logo CLB:</label>
                <input type="file" id="logo" name="logo" style="margin-bottom: 15px;">
                
                <button type="submit" name="create_club" class="btn-submit">Tạo CLB</button>
            </form>
        </div>
    </div>
</div>

<div class="content-box">
    <h4>Danh sách CLB (Tổng: <?php echo count($clubs); ?>)</h4>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Logo</th> <th>Tên CLB</th>
                <th>Mô tả</th>
                <th>Chủ nhiệm</th>
                <th>Số thành viên</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clubs as $club): ?>
            <tr>
                <td><?php echo $club['club_id']; ?></td>
                
                <td>
                    <?php if ($club['logo_url']): ?>
                        <img src="<?php echo htmlspecialchars($club['logo_url']); ?>" 
                             alt="Logo" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                    <?php else: ?>
                        <span style="color: #999; font-size: 0.9em;">Chưa có</span>
                    <?php endif; ?>
                </td>
                
                <td><?php echo htmlspecialchars($club['name']); ?></td>
                <td><?php echo htmlspecialchars($club['description']); ?></td>
                <td>
                    <?php echo htmlspecialchars($club['leader_name'] ?? '-- Chưa gán --'); ?>
                </td>
                <td style="text-align: center; font-weight: bold;">
                    <?php echo $club['member_count']; ?>
                </td>
                <td>
                    <a href="index.php?page=admin/edit_club&club_id=<?php echo $club['club_id']; ?>" 
                       style="text-decoration: none; color: #007bff;">Sửa</a> | 
                    <a href="index.php?action=delete_club&club_id=<?php echo $club['club_id']; ?>" 
                       style="text-decoration: none; color: red;" 
                       onclick="return confirm('CẢNH BÁO: Xóa CLB này?');">Xóa</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($clubs)): ?>
            <tr><td colspan="7" style="text-align: center;">Chưa có CLB nào.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php if ($total_pages = 1): ?>
<div class="pagination" style="margin-top:20px; text-align:center;">

    <!-- Nút Prev -->
    <?php if ($page > 1): ?>
        <a href="index.php?page=admin/manage_all_clubs&p=<?php echo $page - 1; ?>" 
           style="padding: 6px 12px; border:1px solid #ccc; margin-right:4px;">« Trước</a>
    <?php endif; ?>

    <!-- Các số trang -->
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="index.php?page=admin/manage_all_clubs&p=<?php echo $i; ?>"
           style="padding: 6px 12px; border:1px solid #ccc; margin-right:4px;
                  <?php echo ($i == $page ? 'background:#007bff;color:#fff;' : ''); ?>">
           <?php echo $i; ?>
        </a>
    <?php endfor; ?>

    <!-- Nút Next -->
    <?php if ($page < $total_pages): ?>
        <a href="index.php?page=admin/manage_all_clubs&p=<?php echo $page + 1; ?>" 
           style="padding: 6px 12px; border:1px solid #ccc; margin-left:4px;">Sau »</a>
    <?php endif; ?>

</div>
<?php endif; ?>
</div>

<script src="assets/js/script.js"></script>

<script>
    // 1. Gọi hàm từ file script.js để kích hoạt modal này
    setupModal("clubModal", "openModalBtn");

    // 2. Logic riêng của PHP (Bắt buộc phải để ở đây vì JS ngoài không đọc được biến PHP)
    // Nếu có thông báo (lỗi/thành công), tự động mở lại Modal
    <?php if (!empty($message)): ?>
        document.getElementById("clubModal").style.display = "block";
    <?php endif; ?>
</script>