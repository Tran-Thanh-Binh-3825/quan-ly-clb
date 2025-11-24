<?php
// File: handlers/admin_kick_member.php
if ($role !== 'admin' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$member_id = $_POST['member_id'];
$club_id = $_POST['club_id'];

try {
    $sql = "DELETE FROM club_members WHERE member_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$member_id]);
    header('Location: index.php?page=admin/edit_club&club_id=' . $club_id . '&kick=success');
    exit;
} catch (PDOException $e) {
    header('Location: index.php?page=admin/edit_club&club_id=' . $club_id . '&error=' . urlencode($e->getMessage()));
    exit;
}
?>