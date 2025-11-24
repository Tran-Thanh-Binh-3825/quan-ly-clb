<?php
// File: handlers/leader_create_event.php
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

$title = $_POST['title'];
$desc = $_POST['description'];
$loc = $_POST['location'];
$start = $_POST['start_time'];
$end = !empty($_POST['end_time']) ? $_POST['end_time'] : NULL;

try {
    $sql = "INSERT INTO events (club_id, title, description, start_time, end_time, location, created_by_id)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$club_id, $title, $desc, $start, $end, $loc, $leader_id]);
    header('Location: index.php?page=leader/manage_events&create=success');
    exit;
} catch (PDOException $e) {
    header('Location: index.php?page=leader/manage_events&error=' . urlencode($e->getMessage()));
    exit;
}
?>