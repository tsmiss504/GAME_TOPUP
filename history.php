<?php 
require_once 'header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>

<div class="container my-5">
    <h4 class="fw-bold mb-4"><i class="fa-solid fa-clock-rotate-left text-primary me-2"></i>ประวัติการสั่งซื้อสินค้า</h4>

    <div class="card border shadow-sm p-3 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>ชื่อสินค้า</th>
                        <th>ราคา</th>
                        <th>ข้อมูลสินค้าที่ได้รับ</th>
                        <th>วันที่สั่งซื้อ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($orders) === 0): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">ยังไม่มีประวัติการสั่งซื้อสินค้า</td></tr>
                    <?php else: ?>
                        <?php foreach ($orders as $index => $order): ?>
                            <tr>
                                <td><?= $index + 1; ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($order['product_name']); ?></td>
                                <td class="text-primary fw-bold">฿<?= number_format($order['price'], 2); ?></td>
                                <td>
                                    <div class="bg-light p-2 rounded border font-monospace small text-break">
                                        <?= htmlspecialchars($order['item_code']); ?>
                                    </div>
                                </td>
                                <td class="text-muted small"><?= $order['created_at']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
