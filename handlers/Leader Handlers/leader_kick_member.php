<?php
// File: handlers/leader_kick_member.php
if ($role !== 'leader' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$leader_id = getUserId();
$club_id = getLeaderClubId($pdo, $leader_id);
$member_id = $_POST['member_id'];

if (!$club_id) { 
    header('Location: index.php');
    exit;
}
    
try {
    $sql = "DELETE FROM club_members WHERE member_id = ? AND club_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$member_id, $club_id]);
    header('Location: index.php?page=leader/manage_club&kick=success');
    exit;
} catch (PDOException $e) {
    header('Location: index.php?page=leader/manage_club&error=' . urlencode($e->getMessage()));
    exit;
}
?>