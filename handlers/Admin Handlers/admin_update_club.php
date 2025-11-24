<?php
// File: handlers/admin_update_club.php
if ($role !== 'admin' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$club_id = $_POST['club_id'];
$name = $_POST['name'];
$description = $_POST['description'];
$current_logo_path = $_POST['current_logo'];
$logo_path_to_update = $current_logo_path;

if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
    $target_dir = "uploads/";
    $file_extension = strtolower(pathinfo($_FILES["logo"]["name"], PATHINFO_EXTENSION));
    $safe_filename = uniqid() . '_' . time() . '.' . $file_extension;
    $target_file = $target_dir . $safe_filename;
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    
    if (in_array($file_extension, $allowed_types)) {
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $target_file)) {
            $logo_path_to_update = $target_file;
            if (!empty($current_logo_path) && file_exists($current_logo_path)) {
                unlink($current_logo_path); 
            }
        } else {
            $error = 'Lỗi: Không thể di chuyển tệp tải lên.';
            header('Location: index.php?page=admin/edit_club&club_id=' . $club_id . '&error=' . urlencode($error));
            exit;
        }
    } else {
        $error = 'Lỗi: Chỉ chấp nhận các tệp ảnh (JPG, JPEG, PNG, GIF).';
        header('Location: index.php?page=admin/edit_club&club_id=' . $club_id . '&error=' . urlencode($error));
        exit;
    }
}

try {
    $sql_update = "UPDATE clubs SET name = ?, description = ?, logo_url = ? WHERE club_id = ?";
    $stmt_update = $pdo->prepare($sql_update);
    $stmt_update->execute([$name, $description, $logo_path_to_update, $club_id]);
    header('Location: index.php?page=admin/manage_all_clubs&update=success');
    exit;
} catch (PDOException $e) {
    $error = 'Lỗi CSDL: ' . $e->getMessage();
    header('Location: index.php?page=admin/edit_club&club_id=' . $club_id . '&error=' . urlencode($error));
    exit;
}
?>