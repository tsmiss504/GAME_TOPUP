<?php 
require_once 'header.php'; 

$selectedCat = $_GET['id'] ?? null;

// Fetch all categories
$categories = $pdo->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll();

// Fetch products based on category filter
if ($selectedCat) {
    $stmtProd = $pdo->prepare("
        SELECT p.*, COUNT(s.id) as stock_count 
        FROM products p 
        LEFT JOIN stocks s ON p.id = s.product_id AND s.is_sold = 0 
        WHERE p.category_id = ? 
        GROUP BY p.id
    ");
    $stmtProd->execute([$selectedCat]);
    $products = $stmtProd->fetchAll();
} else {
    $products = $pdo->query("
        SELECT p.*, COUNT(s.id) as stock_count 
        FROM products p 
        LEFT JOIN stocks s ON p.id = s.product_id AND s.is_sold = 0 
        GROUP BY p.id
    ")->fetchAll();
}
?>

<div class="container my-4">
    <h4 class="fw-bold mb-4"><i class="fa-solid fa-layer-group text-primary me-2"></i>หมวดหมู่สินค้าทั้งหมด</h4>

    <!-- Categories Tab Filter -->
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="categories.php" class="btn <?= !$selectedCat ? 'btn-dark' : 'btn-outline-dark'; ?>">ทั้งหมด</a>
        <?php foreach ($categories as $cat): ?>
            <a href="categories.php?id=<?= $cat['id']; ?>" class="btn <?= $selectedCat == $cat['id'] ? 'btn-dark' : 'btn-outline-dark'; ?>">
                <?= htmlspecialchars($cat['name']); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Products List -->
    <div class="row g-3">
        <?php if (count($products) === 0): ?>
            <div class="col-12"><div class="alert alert-light border text-center py-5">ไม่พบสินค้าในหมวดหมู่นี้</div></div>
        <?php else: ?>
            <?php foreach ($products as $item): ?>
                <div class="col-12 col-md-4 col-lg-3">
                    <div class="card bg-white border shadow-sm h-100">
                        <img src="<?= htmlspecialchars($item['image_url'] ?: 'https://via.placeholder.com/400x250'); ?>" class="card-img-top" style="height: 180px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title fw-bold text-dark"><?= htmlspecialchars($item['name']); ?></h6>
                            <p class="text-muted small flex-grow-1"><?= htmlspecialchars($item['description']); ?></p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-bold text-primary fs-5">฿<?= number_format($item['price'], 2); ?></span>
                                <span class="badge bg-<?= $item['stock_count'] > 0 ? 'success' : 'danger'; ?>-subtle text-<?= $item['stock_count'] > 0 ? 'success' : 'danger'; ?> border">
                                    <?= $item['stock_count'] > 0 ? 'คงเหลือ ' . $item['stock_count'] : 'สินค้าหมด'; ?>
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
