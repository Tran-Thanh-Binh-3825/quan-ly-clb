<?php
// File: handlers/leader_create_announcement.php
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
$content = $_POST['content'];

try {
    $sql = "INSERT INTO announcements (club_id, title, content, author_id)
            VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$club_id, $title, $content, $leader_id]);
    header('Location: index.php?page=leader/manage_announcements&create=success');
    exit;
} catch (PDOException $e) {
    header('Location: index.php?page=leader/manage_announcements&error=' . urlencode($e->getMessage()));
    exit;
}
?>