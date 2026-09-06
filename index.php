<?php 
require_once 'header.php'; 

// Fetch sliders
$sliders = $pdo->query("SELECT * FROM sliders ORDER BY id DESC")->fetchAll();

// Fetch statistics
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalStock = $pdo->query("SELECT COUNT(*) FROM stocks WHERE is_sold = 0")->fetchColumn();
$totalSold = $pdo->query("SELECT COUNT(*) FROM stocks WHERE is_sold = 1")->fetchColumn();

// Fetch recommended categories
$recCategories = $pdo->query("SELECT * FROM categories WHERE is_recommended = 1")->fetchAll();

// Fetch recommended products
$recProducts = $pdo->query("
    SELECT p.*, COUNT(s.id) as stock_count 
    FROM products p 
    LEFT JOIN stocks s ON p.id = s.product_id AND s.is_sold = 0 
    WHERE p.is_recommended = 1 
    GROUP BY p.id
")->fetchAll();
?>

<div class="container my-4">
    <!-- Slider (1300x400 px) -->
    <?php if (count($sliders) > 0): ?>
    <div id="homeSlider" class="carousel slide mb-4 shadow-sm rounded-4 overflow-hidden" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php foreach ($sliders as $index => $slide): ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : ''; ?>">
                    <img src="<?= htmlspecialchars($slide['image_url']); ?>" class="d-block w-100" style="height: 400px; object-fit: cover;" alt="Banner">
                </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#homeSlider" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#homeSlider" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
    <?php endif; ?>

    <!-- 3 Stat Blocks -->
    <div class="row g-3 mb-5">
        <div class="col-md-4">
            <div class="card bg-white border shadow-sm text-center p-3">
                <i class="fa-solid fa-users text-primary fs-2 mb-2"></i>
                <h6 class="text-muted mb-1">จำนวนสมาชิกทั้งหมด</h6>
                <h3 class="fw-bold text-dark m-0"><?= number_format($totalUsers); ?> คน</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-white border shadow-sm text-center p-3">
                <i class="fa-solid fa-boxes-stacked text-success fs-2 mb-2"></i>
                <h6 class="text-muted mb-1">สินค้าพร้อมจำหน่ายในสต็อก</h6>
                <h3 class="fw-bold text-success m-0"><?= number_format($totalStock); ?> ชิ้น</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-white border shadow-sm text-center p-3">
                <i class="fa-solid fa-cart-check text-info fs-2 mb-2"></i>
                <h6 class="text-muted mb-1">สินค้าที่จำหน่ายไปแล้ว</h6>
                <h3 class="fw-bold text-primary m-0"><?= number_format($totalSold); ?> ชิ้น</h3>
            </div>
        </div>
    </div>

    <!-- Recommended Categories -->
    <?php if (count($recCategories) > 0): ?>
    <div class="mb-5">
        <div class="d-flex align-items-center mb-3">
            <h4 class="fw-bold m-0"><i class="fa-solid fa-fire text-danger me-2"></i>หมวดหมู่แนะนำ</h4>
        </div>
        <div class="row g-3">
            <?php foreach ($recCategories as $cat): ?>
                <div class="col-6 col-md-3">
                    <a href="categories.php?id=<?= $cat['id']; ?>" class="btn btn-outline-dark w-100 py-3 shadow-sm bg-white border fw-semibold">
                        <?= htmlspecialchars($cat['name']); ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Recommended Products -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold m-0"><i class="fa-solid fa-star text-warning me-2"></i>สินค้าแนะนำ</h4>
            <a href="categories.php" class="text-decoration-none text-dark fw-medium">ดูทั้งหมด <i class="fa-solid fa-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-3">
            <?php if (count($recProducts) === 0): ?>
                <div class="col-12"><div class="alert alert-light border text-center py-4">ยังไม่มีสินค้าแนะนำในขณะนี้</div></div>
            <?php else: ?>
                <?php foreach ($recProducts as $item): ?>
                    <div class="col-12 col-md-4 col-lg-3">
                        <div class="card bg-white border shadow-sm h-100">
                            <img src="<?= htmlspecialchars($item['image_url'] ?: 'https://via.placeholder.com/400x250'); ?>" class="card-img-top" style="height: 180px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title fw-bold text-dark"><?= htmlspecialchars($item['name']); ?></h6>
                                <p class="text-muted small flex-grow-1"><?= htmlspecialchars($item['description']); ?></p>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="fw-bold text-primary fs-5">฿<?= number_format($item['price'], 2); ?></span>
                                    <span class="badge bg-<?= $item['stock_count'] > 0 ? 'success' : 'danger'; ?>-subtle text-<?= $item['stock_count'] > 0 ? 'success' : 'danger'; ?> border">
                                        <?= $item['stock_count'] > 0 ? 'พร้อมส่ง ' . $item['stock_count'] : 'สินค้าหมด'; ?>
                                    </span>
                                </div>
                                <button onclick="buyItem(<?= $item['id']; ?>, '<?= htmlspecialchars($item['name'], ENT_QUOTES); ?>', <?= $item['price']; ?>, <?= $item['stock_count']; ?>)" class="btn btn-dark w-100 fw-semibold" <?= $item['stock_count'] == 0 ? 'disabled' : ''; ?>>
                                    <i class="fa-solid fa-cart-shopping me-1"></i> ซื้อสินค้า
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function buyItem(id, name, price, stock) {
    if (stock <= 0) {
        Swal.fire('สินค้าหมด', 'ขออภัย สินค้าชิ้นนี้หมดสต็อกชั่วคราว', 'warning');
        return;
    }
    Swal.fire({
        title: 'ยืนยันการสั่งซื้อ',
        html: `คุณต้องการซื้อ <b>${name}</b><br>ในราคา <b class="text-primary">฿${price.toFixed(2)}</b> ใช่หรือไม่?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#111827',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ยืนยันสั่งซื้อ',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('buy.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'product_id=' + id
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        title: 'ซื้อสินค้าสำเร็จ!',
                        html: `ข้อมูลสินค้าที่คุณได้รับ:<br><div class="bg-light p-3 border rounded text-break font-monospace my-2">${data.code}</div>`,
                        icon: 'success'
                    }).then(() => location.reload());
                } else {
                    Swal.fire('สั่งซื้อไม่สำเร็จ', data.message, 'error');
                }
            })
            .catch(() => Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดจากระบบ', 'error'));
        }
    });
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
