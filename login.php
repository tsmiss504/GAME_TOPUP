<?php
require_once 'db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['credit'] = $user['credit'];
        $_SESSION['user_img'] = $user['profile_img'];
        header("Location: index.php");
        exit;
    } else {
        $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เข้าสู่ระบบ - <?= htmlspecialchars($webSetting['site_name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background-color: #f8fafc; font-family: 'Kanit', sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .card { border-radius: 16px; border: 1px solid #e2e8f0; }
    </style>
</head>
<body>

<div class="container" style="max-width: 420px;">
    <div class="card shadow-lg p-4 bg-white">
        <div class="text-center mb-4">
            <h3 class="fw-bold">เข้าสู่ระบบ</h3>
            <p class="text-muted small">ยินดีต้อนรับกลับสู่ <?= htmlspecialchars($webSetting['site_name']); ?></p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 small"><?= $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-medium">ชื่อผู้ใช้ (Username)</label>
                <input type="text" name="username" class="form-control py-2" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">รหัสผ่าน (Password)</label>
                <input type="password" name="password" class="form-control py-2" required>
            </div>
            <button type="submit" class="btn btn-dark w-100 py-2 fw-semibold mb-3">เข้าสู่ระบบ</button>
        </form>

        <div class="text-center">
            <small class="text-muted">ยังไม่มีบัญชีสมาชิก? <a href="register.php" class="text-decoration-none fw-bold">สมัครสมาชิก</a></small><br>
            <small><a href="index.php" class="text-muted">กลับหน้าหลัก</a></small>
        </div>
    </div>
</div>

</body>
</html>
