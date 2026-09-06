<?php
require_once 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'กรุณาล็อกอินก่อนทำการสั่งซื้อ']);
    exit;
}

$userId = $_SESSION['user_id'];
$productId = $_POST['product_id'] ?? 0;

try {
    $pdo->beginTransaction();

    // Check Product
    $stmtProd = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmtProd->execute([$productId]);
    $product = $stmtProd->fetch();

    if (!$product) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'ไม่พบข้อมูลสินค้า']);
        exit;
    }

    // Check User Balance
    $stmtUser = $pdo->prepare("SELECT * FROM users WHERE id = ? FOR UPDATE");
    $stmtUser->execute([$userId]);
    $user = $stmtUser->fetch();

    if ($user['credit'] < $product['price']) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'ยอดเงินคงเหลือของคุณไม่พอ กรุณาเติมเงิน']);
        exit;
    }

    // Check Stock
    $stmtStock = $pdo->prepare("SELECT * FROM stocks WHERE product_id = ? AND is_sold = 0 LIMIT 1 FOR UPDATE");
    $stmtStock->execute([$productId]);
    $stock = $stmtStock->fetch();

    if (!$stock) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'สินค้าหมดสต็อก']);
        exit;
    }

    // Deduct Credit
    $newCredit = $user['credit'] - $product['price'];
    $stmtDeduct = $pdo->prepare("UPDATE users SET credit = ? WHERE id = ?");
    $stmtDeduct->execute([$newCredit, $userId]);

    // Mark Stock as Sold
    $stmtUpdateStock = $pdo->prepare("UPDATE stocks SET is_sold = 1, user_id = ?, sold_at = NOW() WHERE id = ?");
    $stmtUpdateStock->execute([$userId, $stock['id']]);

    // Save Order History
    $stmtOrder = $pdo->prepare("INSERT INTO orders (user_id, product_name, price, item_code) VALUES (?, ?, ?, ?)");
    $stmtOrder->execute([$userId, $product['name'], $product['price'], $stock['data_content']]);

    $pdo->commit();

    $_SESSION['credit'] = $newCredit;

    echo json_encode([
        'status' => 'success',
        'message' => 'สั่งซื้อสินค้าเรียบร้อยแล้ว',
        'code' => $stock['data_content']
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => 'ระบบเกิดข้อผิดพลาด: ' . $e->getMessage()]);
}
?>
