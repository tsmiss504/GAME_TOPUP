<?php
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'กรุณาเข้าสู่ระบบก่อนเติมเงิน']);
    exit;
}

$voucherUrl = trim($_POST['voucher_url'] ?? '');
$phone = $webSetting['wallet_phone'];

if (empty($voucherUrl)) {
    echo json_encode(['status' => 'error', 'message' => 'กรุณากรอกลิงก์ซองอั่งเปา']);
    exit;
}

if (empty($phone)) {
    echo json_encode(['status' => 'error', 'message' => 'ระบบยังไม่ได้ตั้งค่าเบอร์รับเงิน TrueMoney Wallet']);
    exit;
}

// Extract voucher code from URL
preg_match('/v=([a-zA-Z0-9]+)/', $voucherUrl, $matches);
$voucherCode = $matches[1] ?? '';

if (empty($voucherCode)) {
    echo json_encode(['status' => 'error', 'message' => 'รูปแบบลิงก์ซองอั่งเปาไม่ถูกต้อง']);
    exit;
}

/*
 * หมายเหตุ: ส่วนนี้สามารถนำโค้ดเชื่อมต่อกับ TrueMoney Wallet API จริงมาแทนที่ได้
 * ตัวอย่างจำลองการรับเงินเมื่อใส่ลิงก์ถูกต้อง
 */
$amount = 100.00; // จำลองยอดเงินรับเข้า 100 บาท

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("UPDATE users SET credit = credit + ?, total_topup = total_topup + ? WHERE id = ?");
    $stmt->execute([$amount, $amount, $_SESSION['user_id']]);

    $pdo->commit();

    // Update Session
    $_SESSION['credit'] += $amount;

    echo json_encode([
        'status' => 'success',
        'message' => 'เติมเงินสำเร็จจำนวน ' . number_format($amount, 2) . ' บาท'
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
}
?>
