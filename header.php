<?php require_once 'db.php'; ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($webSetting['site_title']); ?></title>
    <!-- Google Fonts Kanit -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background-color: #ffffff; color: #212529; font-family: 'Kanit', sans-serif; }
        .bg-white-custom { background-color: #ffffff; border-bottom: 1px solid #eaeaea; }
        .avatar-img { width: 42px; height: 42px; object-fit: cover; border-radius: 50%; border: 2px solid #e2e8f0; }
        .card { border-radius: 12px; transition: transform 0.2s, shadow 0.2s; }
        .card:hover { transform: translateY(-3px); }
        .btn-dark { background-color: #111827; border-color: #111827; border-radius: 8px; }
        .btn-dark:hover { background-color: #1f2937; border-color: #1f2937; }
        .btn-outline-dark { border-radius: 8px; }
        .dropdown-menu { border-radius: 12px; border: 1px solid #f0f0f0; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-white-custom sticky-top py-3 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-dark fs-4" href="index.php">
            <i class="fa-solid fa-gamepad text-primary me-2"></i><?= htmlspecialchars($webSetting['site_name']); ?>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                <li class="nav-item"><a class="nav-link text-dark fw-medium" href="index.php"><i class="fa-solid fa-house me-1"></i> หน้าแรก</a></li>
                <li class="nav-item"><a class="nav-link text-dark fw-medium" href="categories.php"><i class="fa-solid fa-layer-group me-1"></i> หมวดหมู่สินค้า</a></li>
                <li class="nav-item"><a class="nav-link text-dark fw-medium" href="topup.php"><i class="fa-solid fa-wallet me-1"></i> เติมเงิน</a></li>
                <li class="nav-item"><a class="nav-link text-dark fw-medium" href="<?= htmlspecialchars($webSetting['contact_link']); ?>" target="_blank"><i class="fa-solid fa-headset me-1"></i> ติดต่อสอบถาม</a></li>
            </ul>

            <div class="d-flex align-items-center">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" id="userMenu" data-bs-toggle="dropdown">
                            <img src="<?= htmlspecialchars($_SESSION['user_img'] ?? 'https://api.dicebear.com/7.x/bottts/svg?seed=User'); ?>" class="avatar-img me-2">
                            <div class="d-flex flex-column text-start">
                                <span class="fw-bold leading-none me-1"><?= htmlspecialchars($_SESSION['username']); ?></span>
                                <span class="badge bg-light text-dark border align-self-start" style="font-size: 11px;">฿<?= number_format($_SESSION['credit'] ?? 0, 2); ?></span>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg p-2 mt-2" style="min-width: 220px;">
                            <li class="px-3 py-2 bg-light rounded mb-2">
                                <small class="text-muted d-block">เครดิตคงเหลือ</small>
                                <strong class="text-success fs-5">฿<?= number_format($_SESSION['credit'] ?? 0, 2); ?></strong>
                            </li>
                            <li><a class="dropdown-item py-2 rounded" href="history.php"><i class="fa-solid fa-clock-rotate-left me-2"></i> ประวัติการซื้อ</a></li>
                            <?php if ($_SESSION['role'] === 'Admin'): ?>
                                <li><a class="dropdown-item py-2 rounded text-danger fw-bold" href="admin/index.php"><i class="fa-solid fa-gauge-high me-2"></i> ระบบหลังบ้าน</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item py-2 rounded text-muted" href="logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i> ออกจากระบบ (Logout)</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-dark me-2 px-3"><i class="fa-solid fa-right-to-bracket me-1"></i> เข้าสู่ระบบ</a>
                    <a href="register.php" class="btn btn-dark px-3"><i class="fa-solid fa-user-plus me-1"></i> สมัครสมาชิก</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
