<?php
// File: handlers/leader_delete_event.php
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

$event_id = $_GET['event_id'] ?? 0;

try {
    $sql = "DELETE FROM events WHERE event_id = ? AND club_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$event_id, $club_id]);
    header('Location: index.php?page=leader/manage_events&delete=success');
    exit;
} catch (PDOException $e) {
    header('Location: index.php?page=leader/manage_events&error=' . urlencode($e->getMessage()));
    exit;
}
?>