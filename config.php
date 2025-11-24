<?php
//  THÔNG TIN KẾT NỐI CSDL
$db_host = 'localhost';
$db_name = 'qlclb_db';
$db_user = 'root';
$db_pass = 'Binhboong1';

//  TẠO KẾT NỐI PDO
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}

?>