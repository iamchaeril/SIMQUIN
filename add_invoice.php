<?php
include 'config.php';

function format_rupiah($angka)
{
    return "Rp " . number_format($angka, 0, ',', '.');
}

if (!isset($_GET['quotation_id'])) {
    echo "ID quotation tidak ditemukan.";
    exit;
}

$quotation_id = intval($_GET['quotation_id']);

// Ambil data quotation
$quotation_sql = "SELECT 
    quotations.id AS quotation_id,
    quotations.nomor AS quotation_number,
    quotations.tanggal AS quotation_date,
    quotations.is_approved,

    quotation_items.deskripsi,
    quotation_items.qty,
    quotation_items.harga,
    

    clients.id AS client_id,
    clients.client_name,
    clients.attn,
    clients.client_address,
    clients.email,
    clients.phone
   

FROM quotations
JOIN quotation_items ON quotations.id = quotation_items.quotation_id
JOIN clients ON quotations.client_id = clients.id WHERE quotations.id = $quotation_id";
$quotation_result = mysqli_query($conn, $quotation_sql);
$quotation = mysqli_fetch_assoc($quotation_result);

// Ambil item quotation
$items_sql = "SELECT * FROM quotation_items WHERE quotation_id = $quotation_id";
$items_result = mysqli_query($conn, $items_sql);
$items = [];
while ($row = mysqli_fetch_assoc($items_result)) {
    $items[] = $row;
}


?>

<?php
$pageTitle = "Form Tambah Invoice";
include 'template/header.php';
include 'template/navbar.php';
?>


<div class="container content pt-4">
    <div class="content-card p-4">
        <h2 class="text-center">Form Invoice</h2>
        <form action="save_invoice.php" method="post">
            <div class="row">

                <div class="col-sm-2 mb-3">
                    <label>Nomor Invoice</label>
                    <input type="text" name="invoice_number" class="form-control" id="invoice_number" readonly />
                </div>
                <div class="col-sm-2 mb-3">
                    <label>Tanggal Invoice</label>
                    <input type="date" name="invoice_date" class="form-control" required>
                </div>
                <div class="col-sm-2 mb-3">
                    <label>Nomor Referensi</label>
                    <input type="text" class="form-control" value="<?= $quotation['quotation_number'] ?>" disabled>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4 mb-3">
                    <label>Client</label>
                    <input type="text" class="form-control" value="<?= $quotation['client_name'] ?>" disabled>
                </div>
                <div class="col-sm-2 mb-3">
                    <label>Attention</label>
                    <input type="text" class="form-control" value="<?= $quotation['attn'] ?>" disabled>
                </div>
                <div class="col-sm-2 mb-3">
                    <label>Phone</label>
                    <input type="text" class="form-control" value="<?= $quotation['phone'] ?>" disabled>
                </div>
                <div class="col-sm-4 mb-3">
                    <label>Email</label>
                    <input type="text" class="form-control" value="<?= $quotation['email'] ?>" disabled>
                </div>


            </div>


            <h5>Item Invoice</h5>
            <?php foreach ($items as $item): ?>
                <div class="row mb-2">
                    <div class="col">
                        <input type="text" name="deskripsi[]" class="form-control" value="<?= $item['deskripsi'] ?>" required>
                    </div>
                    <div class="col-2">
                        <input type="number" name="qty[]" class="form-control" value="<?= $item['qty'] ?>" required>
                    </div>
                    <div class="col-3">
                        <!-- Untuk tampilan -->
                        <input type="text" class="form-control" value="<?= format_rupiah($item['harga']) ?>" readonly>

                        <!-- Untuk kirim data ke server -->
                        <input type="hidden" name="harga[]" value="<?= $item['harga'] ?>">
                    </div>
                </div>
            <?php endforeach; ?>
            <input type="hidden" name="quotation_id" value="<?= $quotation_id ?>">
            <button type="submit" class="btn btn-primary">Simpan Invoice</button>
            <a href="quotations.php" class="btn btn-secondary">Kembali</a>
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




</body>

</html>