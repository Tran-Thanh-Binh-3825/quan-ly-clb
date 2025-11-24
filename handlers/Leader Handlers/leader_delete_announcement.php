<?php
// File: handlers/leader_delete_announcement.php
if ($role !== 'leader') {
    header('Location: index.php');
    exit;
}

$leader_id = getUserId();
$club_id = getLeaderClubId($pdo, $leader_id);

if (!$club_id) {
    header('Location: index.php');
    exit;
}

$announcement_id = $_GET['announcement_id'] ?? 0;

try {
    $sql = "DELETE FROM announcements WHERE announcement_id = ? AND club_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$announcement_id, $club_id]);
    header('Location: index.php?page=leader/manage_announcements&delete=success');
    exit;
} catch (PDOException $e) {
    header('Location: index.php?page=leader/manage_announcements&error=' . urlencode($e->getMessage()));
    exit;
}
?>