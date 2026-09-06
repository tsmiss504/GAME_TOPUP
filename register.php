<?php
require_once 'db.php';

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    if ($password !== $confirm_password) {
        $error = 'รหัสผ่านทั้งสองช่องไม่ตรงกัน';
    } else {
        $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmtCheck->execute([$username]);
        if ($stmtCheck->fetch()) {
            $error = 'ชื่อผู้ใช้นี้มีในระบบแล้ว';
        } else {
            $hashPass = password_hash($password, PASSWORD_DEFAULT);
            $profileImg = 'https://api.dicebear.com/7.x/bottts/svg?seed=' . urlencode($username);
            
            $stmtInsert = $pdo->prepare("INSERT INTO users (username, password, role, credit, total_topup, profile_img) VALUES (?, ?, 'User', 0.00, 0.00, ?)");
            $stmtInsert->execute([$username, $hashPass, $profileImg]);
            $success = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>สมัครสมาชิก - <?= htmlspecialchars($webSetting['site_name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background-color: #f8fafc; font-family: 'Kanit', sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .card { border-radius: 16px; border: 1px solid #e2e8f0; }
    </style>
</head>
<body>

<div class="container" style="max-width: 440px;">
    <div class="card shadow-lg p-4 bg-white">
        <div class="text-center mb-4">
            <h3 class="fw-bold">สมัครสมาชิก</h3>
            <p class="text-muted small">สร้างบัญชีผู้ใช้ใหม่ในระบบ</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 small"><?= $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'สมัครสมาชิกสำเร็จ!',
                        text: 'คุณสามารถเข้าสู่ระบบได้ทันที',
                        icon: 'success'
                    }).then(() => {
                        window.location = 'login.php';
                    });
                });
            </script>
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
            <div class="mb-3">
                <label class="form-label fw-medium">ยืนยันรหัสผ่าน</label>
                <input type="password" name="confirm_password" class="form-control py-2" required>
            </div>
            <button type="submit" class="btn btn-dark w-100 py-2 fw-semibold mb-3">สมัครสมาชิก</button>
        </form>

        <div class="text-center">
            <small class="text-muted">มีบัญชีผู้ใช้แล้ว? <a href="login.php" class="text-decoration-none fw-bold">เข้าสู่ระบบ</a></small><br>
            <small><a href="index.php" class="text-muted">กลับหน้าหลัก</a></small>
        </div>
    </div>
</div>

</body>
</html>
