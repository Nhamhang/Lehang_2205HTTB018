<?php
// Khởi động session (quan trọng cho login)
session_start();

// THÔNG SỐ DATABASE – chỉnh theo XAMPP của bạn
$host = "localhost";  
$db   = "todo_app";   // tên database bạn đã tạo
$user = "root";       // tài khoản mặc định của XAMPP
$pass = "";           // mật khẩu mặc định XAMPP là rỗng

// Chuỗi kết nối PDO
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    // Tạo PDO
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // báo lỗi
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC         // lấy dạng mảng
    ]);

} catch (PDOException $e) {
    // Nếu kết nối lỗi
    die("Kết nối CSDL thất bại: " . $e->getMessage());
}
