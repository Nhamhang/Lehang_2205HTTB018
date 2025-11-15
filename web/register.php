<?php
require_once 'config.php';  // kết nối PDO + session_start()

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nhận dữ liệu POST
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    // ====== VALIDATION ======
    if ($username === '' || $password === '') {
        $errors[] = "Username và mật khẩu là bắt buộc.";
    }

    if ($password !== $confirm) {
        $errors[] = "Xác nhận mật khẩu không khớp.";
    }

    // Kiểm tra độ dài
    if (strlen($username) < 3) {
        $errors[] = "Username phải có ít nhất 3 ký tự.";
    }

    // ====== Nếu không lỗi → xử lý ======
    if (empty($errors)) {
        // Kiểm tra username hoặc email đã tồn tại chưa
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = "Username hoặc email đã tồn tại.";
        } else {
            // HASH mật khẩu
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Lưu vào DB
            $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
            $stmt->execute([$username, $hashedPassword, $email ?: null]);

            // Chuyển hướng sang login
            header("Location: login.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản</title>
	<style>
        /* Đặt toàn trang có background */
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background-image: url('background.jpg'); /* đường dẫn tới ảnh */
            background-size: cover;     /* ảnh phủ toàn màn hình */
            background-position: center;
            background-repeat: no-repeat;
            font-family: Arial, sans-serif;
        }
		 </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container py-4">

    <h2>Đăng ký tài khoản</h2>

    <!-- Hiển thị lỗi -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $e): ?>
                <p><?= htmlspecialchars($e) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- FORM ĐĂNG KÝ -->
    <form method="POST">

        <div class="mb-3">
            <label class="form-label">Username *</label>
            <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($username ?? '') ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Email (tuỳ chọn)</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email ?? '') ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Mật khẩu *</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Xác nhận mật khẩu *</label>
            <input type="password" name="confirm" class="form-control" required>
        </div>

        <button class="btn btn-primary" type="submit">Đăng ký</button>

        <p class="mt-3">
            Đã có tài khoản? <a href="login.php">Đăng nhập</a>
        </p>

    </form>

</body>
</html>
