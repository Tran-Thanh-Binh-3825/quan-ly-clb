<?php
// File: functions/db_helpers.php

// Hàm lấy Club ID của Leader
function getLeaderClubId($pdo, $leader_id) {
    // (Phải truyền $pdo vào vì tệp này không tự kết nối CSDL)
    try {
        $sql = "SELECT club_id FROM clubs WHERE leader_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$leader_id]);
        $club = $stmt->fetch();
        
        return $club ? $club['club_id'] : null;
        
    } catch (PDOException $e) {
        return null;
    }
}

// (Bạn có thể thêm các hàm trợ giúp khác vào đây trong tương lai)
?>