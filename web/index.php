<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "todo_app");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);


if (isset($_POST['action']) && $_POST['action'] === 'create') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = $_POST['due_date'];

    $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, description, due_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $_SESSION['user_id'], $title, $description, $due_date);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit();
}


if (isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = $_POST['id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = $_POST['due_date'];
    $status = trim($_POST['status']);

    $stmt = $conn->prepare("UPDATE tasks SET title=?, description=?, due_date=?, status=? WHERE id=? AND user_id=?");
    $stmt->bind_param("ssssii", $title, $description, $due_date, $status, $id, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit();
}


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $id, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit();
}


$statusFilter = trim($_GET['status'] ?? '');
$orderBy = $_GET['sort'] ?? 'due_date';
$orderBy = in_array($orderBy, ['due_date','created_at']) ? $orderBy : 'due_date';

$validStatus = ['pending','in_progress','completed'];

if (in_array($statusFilter, $validStatus)) {
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id=? AND status=? ORDER BY $orderBy ASC");
    $stmt->bind_param("is", $_SESSION['user_id'], $statusFilter);
} else {
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id=? ORDER BY $orderBy ASC");
    $stmt->bind_param("i", $_SESSION['user_id']);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>
<style>
        
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background-image: url('index.jpg'); 
            background-size: cover;     
            background-position: center;
            background-repeat: no-repeat;
            font-family: Arial, sans-serif;
        }
		 </style>
<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2, h3 { margin-bottom: 10px; }
input, textarea, select, button { padding: 5px; margin: 2px 0; }
table { border-collapse: collapse; width: 100%; margin-top: 10px; }
th, td { border: 1px solid #ccc; padding: 5px; text-align: left; }
th { background: #f0f0f0; }
.create-form { margin-bottom: 20px; padding: 10px; border: 1px solid #ccc; width: 400px; }
.update-form { display: inline; }
button { cursor: pointer; }
</style>
</head>
<body>

<div style="position:absolute; top:20px; right:20px; font-size:22px; font-weight:bold;">
  Xin chào, <?= htmlspecialchars($_SESSION['username']) ?> 
  <a href="logout.php" style="margin-left:20px; color:#00f; text-decoration:none;">Đăng xuất</a>
</div>

<div class="create-form-wrapper">
<h3>Thêm công việc mới</h3>
<style>
.create-form{
    width: 400px;      
    padding: 20px;
    border: 1px solid #ccc;
    margin: 50px auto;  
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    border-radius: 8px;
}
</style>
<form class="create-form" action="" method="post">
    <input type="hidden" name="action" value="create">
    Tiêu đề:<br>
    <input type="text" name="title" required><br>
    Mô tả:<br>
    <textarea name="description"></textarea><br>
    Ngày hết hạn:<br>
    <input type="date" name="due_date"><br><br>
    <button type="submit">Thêm</button>
</form>
</div>
<style>
.create-form-wrapper {
    width: 400px;                 
    margin: 50px auto;           
    text-align: left;            
}

.create-form-wrapper h3 {
    margin-bottom: 15px;          
    font-size: 24px;              
    font-weight: bold;
    text-align: center;        
}

.create-form {
    width: 100%;                  
    padding: 20px;
    border: 1px solid #ccc;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    border-radius: 8px;
}
</style>

<h3>Danh sách công việc</h3>
<form method="get">
    Lọc trạng thái:
    <select name="status">
        <option value="" <?= $statusFilter===''?'selected':'' ?>>Tất cả</option>
        <option value="pending" <?= $statusFilter==='pending'?'selected':'' ?>>Chưa hoàn thành</option>
        <option value="in_progress" <?= $statusFilter==='in_progress'?'selected':'' ?>>Đang tiến hành</option>
        <option value="completed" <?= $statusFilter==='completed'?'selected':'' ?>>Hoàn thành</option>
    </select>
    
    <button type="submit">Áp dụng</button>
</form>

<table>
<tr>
    <th>Vị trí công việc</th>
    <th>Mô tả</th>
    <th>Ngày hết hạn</th>
    <th>Trạng thái</th>
    <th>Hành động</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>
<form class="update-form" action="" method="post">
<tr>
    <input type="hidden" name="action" value="update">
    <input type="hidden" name="id" value="<?= $row['id'] ?>">

    <td><input type="text" name="title" value="<?= htmlspecialchars($row['title']) ?>"></td>
    <td><textarea name="description"><?= htmlspecialchars($row['description']) ?></textarea></td>
    <td><input type="date" name="due_date" value="<?= $row['due_date'] ?>"></td>
    <td>
        <select name="status">
            <option value="pending" <?= $row['status']=='pending'?'selected':'' ?>>Chưa hoàn thành</option>
            <option value="in_progress" <?= $row['status']=='in_progress'?'selected':'' ?>>Đang tiến hành</option>
            <option value="completed" <?= $row['status']=='completed'?'selected':'' ?>>Hoàn thành</option>
        </select>
    </td>
    <td>
        <button type="submit">Cập nhật</button>
        <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
    </td>
</tr>
</form>
<?php endwhile; ?>
</table>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
