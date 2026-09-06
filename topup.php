<?php require_once 'header.php'; ?>

<div class="container my-5" style="max-width: 520px;">
    <div class="card border shadow-sm p-4 bg-white">
        <div class="text-center mb-4">
            <div class="bg-warning-subtle text-warning d-inline-flex p-3 rounded-circle mb-2">
                <i class="fa-solid fa-wallet fs-1"></i>
            </div>
            <h4 class="fw-bold">เติมเงินด้วย TrueMoney Wallet</h4>
            <p class="text-muted small">ระบบเติมเงินอัตโนมัติผ่านซองอั่งเปา TrueMoney Wallet</p>
        </div>

        <div class="alert alert-info small border-0 bg-light">
            <strong>วิธีเติมเงิน:</strong><br>
            1. เข้าแอป TrueMoney Wallet -> เลือก "ส่งของขวัญ"<br>
            2. สร้างซองอั่งเปา กรอกจำนวนเงินที่ต้องการ<br>
            3. เลือก "แบ่งจำนวนเงินเท่ากัน" ใส่จำนวนคน 1 คน<br>
            4. ก๊อปปี้ลิงก์ซองอั่งเปามาวางในช่องด้านล่าง
        </div>
        
        <form id="topupForm">
            <div class="mb-3">
                <label class="form-label fw-semibold">ลิงก์ซองอั่งเปา</label>
                <input type="url" id="voucher_url" class="form-control py-2" placeholder="https://gift.truemoney.com/v1/?v=..." required>
            </div>
            <button type="submit" class="btn btn-dark w-100 py-2 fw-semibold">
                <i class="fa-solid fa-bolt me-1"></i> ยืนยันการเติมเงิน
            </button>
        </form>
    </div>
</div>

<script>
document.getElementById('topupForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const url = document.getElementById('voucher_url').value;

    Swal.fire({
        title: 'กำลังตรวจสอบซองอั่งเปา...',
        text: 'กรุณารอสักครู่ ระบบกำลังประมวลผล',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    fetch('Voucher.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'voucher_url=' + encodeURIComponent(url)
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            Swal.fire('เติมเงินสำเร็จ!', data.message, 'success').then(() => location.reload());
        } else {
            Swal.fire('เติมเงินไม่สำเร็จ', data.message, 'error');
        }
    })
    .catch(() => Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการเชื่อมต่อระบบ', 'error'));
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
