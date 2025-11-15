<?php
session_start(); 


$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "todo_app"; 

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $dbUsername, $dbPasswordHash);
        $stmt->fetch();

       
        if (password_verify($password, $dbPasswordHash)) {
            
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $dbUsername;

            
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
        
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background-image: url('background.jpg'); 
            background-size: contain;    
            background-position: center;
            background-repeat: no-repeat;
            font-family: Arial, sans-serif;
        }

      
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
