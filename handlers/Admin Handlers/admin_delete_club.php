<?php
// File: handlers/admin_delete_club.php
if ($role !== 'admin') {
    header('Location: index.php');
    exit;
}

$club_id = $_GET['club_id'] ?? 0;

try {
    $sql_delete = "DELETE FROM clubs WHERE club_id = ?";
    $stmt_delete = $pdo->prepare($sql_delete);
    $stmt_delete->execute([$club_id]);
    header('Location: index.php?page=admin/manage_all_clubs&delete=success');
    exit;
} catch (PDOException $e) {
    $error = 'Lỗi CSDL: ' . $e->getMessage();
    header('Location: index.php?page=admin/manage_all_clubs&error=' . urlencode($error));
    exit;
}
?>