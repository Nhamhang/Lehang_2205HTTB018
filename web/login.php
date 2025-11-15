<?php
session_start(); // Bắt đầu session

// Kết nối tới cơ sở dữ liệu MySQL
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "todo_app"; // Thay bằng tên database của bạn

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Xử lý đăng nhập khi form được submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Chuẩn bị câu truy vấn để tránh SQL Injection
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $dbUsername, $dbPasswordHash);
        $stmt->fetch();

        // Kiểm tra mật khẩu
        if (password_verify($password, $dbPasswordHash)) {
            // Lưu thông tin người dùng vào session
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $dbUsername;

            // Chuyển hướng tới trang chính sau khi đăng nhập
            header("Location: index.php");
            exit();
        } else {
            $error = "Mật khẩu không đúng!";
        }
    } else {
        $error = "Tên đăng nhập không tồn tại!";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <style>
        /* Đặt toàn trang có background */
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background-image: url('background.jpg'); /* đường dẫn tới ảnh */
            background-size: contain;     /* ảnh phủ toàn màn hình */
            background-position: center;
            background-repeat: no-repeat;
            font-family: Arial, sans-serif;
        }

        /* Form đăng nhập ở giữa màn hình */
        .login-container {
            width: 500px;
            margin: 0 auto;
            position: relative;
            top: 50%;
            transform: translateY(-50%);
            
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px #000;
        }

        input[type="text"], input[type="password"] {
            width: 90%;
            padding: 10px;
            margin: 5px 0 15px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 95%;
            padding: 10px;
            background: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Đăng nhập</h2>
        <form action="" method="post">
            <label for="username">Tên đăng nhập:</label>
            <input type="text" id="username" name="username" required><br>

            <label for="password">Mật khẩu:</label>
            <input type="password" id="password" name="password" required><br>

            <button type="submit">Đăng nhập</button>
        </form>
    </div>
</body>
</html>
