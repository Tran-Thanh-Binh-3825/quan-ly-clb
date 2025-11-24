<?php
// File: handlers/student_leave_club.php
if ($role !== 'student' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$student_id = getUserId();
$member_id = $_POST['member_id'];

try {
    $sql = "DELETE FROM club_members WHERE member_id = ? AND user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$member_id, $student_id]);
    header('Location: index.php?page=student/my_clubs&leave=success');
    exit;
} catch (PDOException $e) {
    header('Location: index.php?page=student/my_clubs&error=' . urlencode($e->getMessage()));
    exit;
}
?>