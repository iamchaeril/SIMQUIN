<?php
include 'config.php';

function format_rupiah($angka)
{
    return "Rp " . number_format($angka, 0, ',', '.');
}

if (!isset($_GET['invoice_id'])) {
    echo "ID invoice tidak ditemukan.";
    exit;
}

$invoice_id = intval($_GET['invoice_id']);

// Ambil data invoice
$invoice_sql = "SELECT 
    invoices.id AS invoice_id,
    invoices.invoice_number,
    invoices.invoice_date,
    invoices.created_at,
    invoices.is_paid,

    invoice_items.qty,
    invoice_items.deskripsi,
    invoice_items.harga,

    quotations.id AS quotation_id,
    quotations.nomor AS quotation_number,
    quotations.tanggal AS quotation_date,
    quotations.is_approved,

    clients.id AS client_id,
    clients.client_name,
    clients.attn,
    clients.client_address,
    clients.email,
    clients.phone,
    COALESCE(SUM(invoice_items.qty * invoice_items.harga), 0) AS total_harga
FROM invoices
JOIN invoice_items ON invoices.id = invoice_items.invoice_id
JOIN quotations ON invoices.quotation_id = quotations.id
JOIN clients ON quotations.client_id = clients.id WHERE invoices.id = $invoice_id";
$invoice_result = mysqli_query($conn, $invoice_sql);
$invoice = mysqli_fetch_assoc($invoice_result);

// Ambil item quotation
$items_sql = "SELECT * FROM invoice_items WHERE invoice_id = $invoice_id";
$items_result = mysqli_query($conn, $items_sql);
$items = [];
while ($row = mysqli_fetch_assoc($items_result)) {
    $items[] = $row;
}


?>

<?php
$pageTitle = "Form Pembayaran Invoice";
include 'template/header.php';
include 'template/navbar.php';
?>


<div class="container content pt-4">
    <div class="content-card p-4">
        <h2 class="text-center">Form Pembayaran Invoice</h2>
        <form action="save_pembayaran.php" method="post">
            <div class="row">

                <div class="col-sm-2 mb-3">
                    <label>Nomor Invoice</label>
                    <input type="text" name="invoice_number" class="form-control" value="<?= $invoice['invoice_number'] ?>" disabled />
                </div>
                <div class="col-sm-2 mb-3">
                    <label>Tanggal Invoice</label>
                    <input type="date" name="invoice_date" class="form-control" value="<?= $invoice['invoice_date'] ?>" disabled>
                </div>
                <div class="col-sm-2 mb-3">
                    <label>Nomor Referensi</label>
                    <input type="text" class="form-control" value="<?= $invoice['quotation_number'] ?>" disabled>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4 mb-3">
                    <label>Client</label>
                    <input type="text" class="form-control" value="<?= $invoice['client_name'] ?>" disabled>
                </div>
                <div class="col-sm-2 mb-3">
                    <label>Attention</label>
                    <input type="text" class="form-control" value="<?= $invoice['attn'] ?>" disabled>
                </div>
                <div class="col-sm-2 mb-3">
                    <label>Phone</label>
                    <input type="text" class="form-control" value="<?= $invoice['phone'] ?>" disabled>
                </div>
                <div class="col-sm-4 mb-3">
                    <label>Email</label>
                    <input type="text" class="form-control" value="<?= $invoice['email'] ?>" disabled>
                </div>


            </div>


            <h5>Item Invoice</h5>
            <?php foreach ($items as $item): ?>
                <div class="row mb-2">
                    <div class="col">
                        <input type="text" class="form-control" value="<?= $item['deskripsi'] ?>" disabled>
                    </div>
                    <div class="col-2">
                        <input type="number" class="form-control" value="<?= $item['qty'] ?>" disabled>
                    </div>
                    <div class="col-3">
                        <!-- Untuk tampilan -->
                        <input type="text" class="form-control" value="<?= format_rupiah($item['harga']) ?>" disabled>

                        <!-- Untuk kirim data ke server -->
                        <input type="hidden" value="<?= $item['harga'] ?>">
                    </div>
                </div>
            <?php endforeach; ?>
            <br>
            <div class="row">
                <div class="col-9">
                    <h5 class="text-end">Amount Paid</h5>
                </div>
                <div class="col-3">

                    <!-- Hidden input untuk nilai numerik -->
                    <input type="hidden" name="amount_paid" value="<?= $invoice['total_harga'] ?>">

                    <!-- Tampilan hanya untuk user -->
                    <input type="text" class="form-control" value="<?= format_rupiah($invoice['total_harga']) ?>" disabled>

                </div>

            </div>
            <h5>Pembayaran</h5>
            <div class="row">
                <div class="col-sm-2 mb-3">
                    <label>Tanggal Pembayaran</label>
                    <input type="date" name="payment_date" class="form-control" required>
                </div>
                <div class="col-sm-4 mb-3">
                    <label>Metode Pembayaran</label>
                    <select name="payment_method" id="payment_method" class="form-control" required>
                        <option value="" disabled selected>Pilih Metode</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="tunai">Tunai</option>
                        <option value="dana">Dana</option>
                        <option value="gopay">GoPay</option>
                    </select>
                </div>
                <div class="col-sm-6 mb-3">
                    <label>Notes</label>
                    <input type="text" name="notes" class="form-control" required>
                </div>
            </div>


            <input type="hidden" name="invoice_id" value="<?= $invoice_id ?>">
            <button type="submit" class="btn btn-primary">Proses Pembayaran</button>
            <a href="invoices.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>


<?php include 'template/scriptjs.php'; ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const input = document.getElementById('invoice_number');

        function generateQuotationNumber() {
            const year = new Date().getFullYear();
            const letters = Array.from({
                length: 3
            }, () => String.fromCharCode(65 + Math.floor(Math.random() * 26))).join('');
            const numbers = Math.floor(10 + Math.random() * 90); // 2 digit random number (10–99)
            return `INV/${year}/${letters}${numbers}`;
        }

        input.value = generateQuotationNumber();
    });
</script>

<script>
    // SweetAlert jika sukses ditambahkan
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('success') === '1') {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Pembayaran Invoice berhasil disimpan.',
            timer: 2000,
            showConfirmButton: false
        });
        window.history.replaceState({}, document.title, window.location.pathname);
    }
</script>



</body>

</html>