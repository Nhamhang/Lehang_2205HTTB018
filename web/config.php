<?php

session_start();


$host = "localhost";  
$db   = "todo_app";   
$user = "root";       
$pass = "";          


$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    // Tạo PDO
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC         
    ]);

} catch (PDOException $e) {
    // Nếu kết nối lỗi
    die("Kết nối CSDL thất bại: " . $e->getMessage());
}
