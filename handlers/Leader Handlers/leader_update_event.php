<?php
// File: handlers/leader_update_event.php
if ($role !== 'leader' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$leader_id = getUserId();
$club_id = getLeaderClubId($pdo, $leader_id);

if (!$club_id) {
    header('Location: index.php');
    exit;
}

$event_id = $_POST['event_id'];
$title = $_POST['title'];
$desc = $_POST['description'];
$loc = $_POST['location'];
$start = $_POST['start_time'];
$end = !empty($_POST['end_time']) ? $_POST['end_time'] : NULL;

try {
    $sql = "UPDATE events SET 
                title = ?, description = ?, start_time = ?, end_time = ?, location = ?
            WHERE event_id = ? AND club_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$title, $desc, $start, $end, $loc, $event_id, $club_id]);
    header('Location: index.php?page=leader/manage_events&update=success');
    exit;
} catch (PDOException $e) {
    header('Location: index.php?page=leader/edit_event&event_id=' . $event_id . '&error=' . urlencode($e->getMessage()));
    exit;
}
?>