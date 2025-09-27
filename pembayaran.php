<?php
include 'config.php';

function format_rupiah($angka)
{
    return "Rp " . number_format($angka, 0, ',', '.');
}

function render_pagination($page, $totalPages)
{
    if ($totalPages <= 1) return;

    echo '<tr id="pagination-row"><td colspan="8" class="text-center">
            <nav><ul class="pagination justify-content-center">';

    // Tombol sebelumnya
    if ($page > 1) {
        echo '<li class="page-item"><a href="#" class="page-link" data-page="' . ($page - 1) . '">«</a></li>';
    }

    // Navigasi halaman (maksimal 5 ditampilkan + last + ...)
    $max = min($totalPages, 5);
    for ($i = 1; $i <= $totalPages; $i++) {
        if ($i <= $max || $i == $totalPages) {
            if ($i == $page) {
                echo '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
            } else {
                echo '<li class="page-item"><a href="#" class="page-link" data-page="' . $i . '">' . $i . '</a></li>';
            }
        } elseif ($i == $max + 1) {
            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }

    // Tombol selanjutnya
    if ($page < $totalPages) {
        echo '<li class="page-item"><a href="#" class="page-link" data-page="' . ($page + 1) . '">»</a></li>';
    }

    echo '</ul></nav></td></tr>';
}

// AJAX Pagination
if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    // Ambil parameter filter
    $keyword        = isset($_GET['keyword']) ? $conn->real_escape_string($_GET['keyword']) : '';
    $tanggal_dari   = isset($_GET['tanggal_dari']) ? $_GET['tanggal_dari'] : '';
    $tanggal_sampai = isset($_GET['tanggal_sampai']) ? $_GET['tanggal_sampai'] : '';
    $page           = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit          = 10;
    $offset         = ($page - 1) * $limit;

    // Bangun WHERE clause
    $where = "WHERE 1=1";
    if ($keyword !== '') {
        $where .= " AND (clients.client_name LIKE '%$keyword%' OR invoices.invoice_number LIKE '%$keyword%')";
    }
    if ($tanggal_dari !== '' && $tanggal_sampai !== '') {
        $from = date('Y-m-d', strtotime($tanggal_dari));
        $to   = date('Y-m-d', strtotime($tanggal_sampai));
        $where .= " AND DATE(invoice_payments.payment_date) BETWEEN '$from' AND '$to'";
    }

    // Hitung total data
    $countSql = "
        SELECT COUNT(DISTINCT invoices.id) AS total 
        FROM invoices
        JOIN quotations ON invoices.quotation_id = quotations.id
        JOIN invoice_payments ON invoices.id = invoice_payments.invoice_id
        $where
    ";
    $countResult = $conn->query($countSql);
    $totalData   = $countResult ? $countResult->fetch_assoc()['total'] : 0;
    $totalPages  = ceil($totalData / $limit);

    // Query data utama
    $sql = "
        SELECT invoices.*, 
               quotations.nomor AS quotation_nomor, 
               quotations.tanggal AS quotation_tanggal,
               invoice_payments.payment_date,
               invoice_payments.amount_paid, 
               invoice_payments.payment_method, 
               invoice_payments.notes,  
               clients.client_name, 
               clients.attn, 
               clients.phone, 
               clients.client_address,
               COALESCE(SUM(invoice_items.qty * invoice_items.harga), 0) AS total_invoice
        FROM invoices
        JOIN quotations ON invoices.quotation_id = quotations.id
        JOIN clients ON quotations.client_id = clients.id
        JOIN invoice_items ON invoices.id = invoice_items.invoice_id
        JOIN invoice_payments on invoices.id = invoice_payments.invoice_id
        $where
        ORDER BY invoices.id DESC
        LIMIT $limit OFFSET $offset
    ";
    $result = $conn->query($sql);
    $no = $offset + 1;

    // Output hasil dalam bentuk HTML table rows
    ob_start();
    echo '<tbody id="invoice-body">';

    if ($result && $result->num_rows > 0):
        while ($row = $result->fetch_assoc()):
?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['invoice_number'] ?></td>
                <td><?= $row['client_name'] ?></td>
                <td><?= $row['amount_paid'] !== null ? format_rupiah($row['amount_paid']) : '-' ?></td>
                <td><?= $row['payment_method'] ?></td>
                <td><?= $row['notes'] ?></td>
                <td><?= !empty($row['payment_date']) ? date('d-m-Y', strtotime($row['payment_date'])) : '-' ?></td>
                <td>
                    <a href="get_invoice.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-success">PDF</a>
                </td>
            </tr>
        <?php
        endwhile;
        render_pagination($page, $totalPages);
    else:
        ?>
        <tr>
            <td colspan="8" class="text-center">Tidak ada data</td>
        </tr>
<?php
    endif;

    echo '</tbody>';
    echo ob_get_clean();
    exit;
}


// Fallback untuk halaman pertama (non-AJAX load awal)
$sql = " SELECT invoices.*, 
               quotations.nomor AS quotation_nomor, 
               quotations.tanggal AS quotation_tanggal,
               invoice_payments.payment_date,
               invoice_payments.amount_paid, 
               invoice_payments.payment_method, 
               invoice_payments.notes,  
               clients.client_name, 
               clients.attn, 
               clients.phone, 
               clients.client_address,
               COALESCE(SUM(invoice_items.qty * invoice_items.harga), 0) AS total_invoice
        FROM invoices
        JOIN quotations ON invoices.quotation_id = quotations.id
        JOIN clients ON quotations.client_id = clients.id
        JOIN invoice_items ON invoices.id = invoice_items.invoice_id
        JOIN invoice_payments on invoices.id = invoice_payments.invoice_id
        GROUP BY 
            invoices.id,
            invoices.invoice_number,
            invoices.quotation_id,
            quotations.nomor,
            invoice_payments.payment_date,
            clients.client_name
        ORDER BY invoices.id DESC
        LIMIT 10";

$result = $conn->query($sql);

// Hitung total data untuk pagination
$countSql = "SELECT COUNT(DISTINCT invoices.id) AS total 
FROM invoices
JOIN quotations ON invoices.quotation_id = quotations.id
JOIN invoice_payments ON invoices.id = invoice_payments.invoice_id";

$countResult = $conn->query($countSql);
$totalData = $countResult ? (int)$countResult->fetch_assoc()['total'] : 0;
$totalPages = ceil($totalData / 10);
?>

<?php
$pageTitle = "Pembayaran";
$activePage = 'pembayaran';
include 'template/header.php';
include 'template/navbar.php';
?>

<!-- Main Content -->
<div class="container py-2">
    <h2 class="text-center mb-4">Data Pembayaran</h2>
    <div class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" id="search" class="form-control" placeholder="Cari client atau nomor invoice...">
        </div>
        <div class="col-md-3">
            <input type="date" id="tanggal_dari" class="form-control">
        </div>
        <div class="col-auto d-flex align-items-center">s/d</div>
        <div class="col-md-3">
            <input type="date" id="tanggal_sampai" class="form-control">
        </div>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nomor Invoice</th>
                <th>Client</th>
                <th>Jumlah Dibayar</th>
                <th>Metode Pembayaran</th>
                <th>Catatan</th>
                <th>Tanggal Pembayaran</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="invoice-body">
            <?php
            $no = 1;
            while ($row = $result->fetch_assoc()):
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['invoice_number'] ?></td>
                    <td><?= $row['client_name'] ?></td>
                    <td><?= $row['amount_paid'] !== null ? format_rupiah($row['amount_paid']) : '-' ?></td>
                    <td><?= $row['payment_method'] ?></td>
                    <td><?= $row['notes'] ?></td>
                    <td><?= !empty($row['payment_date']) ? date('d-m-Y', strtotime($row['payment_date'])) : '-' ?></td>
                    <td>
                        <a href="get_invoice.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-success">PDF</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            <?php render_pagination(1, $totalPages); ?>
        </tbody>
    </table>
</div>



<?php include 'template/scriptjs.php'; ?>

<script>
    let currentPage = 1;

    function fetchData(page = 1) {
        const keyword = $('#search').val();
        const tanggal_dari = $('#tanggal_dari').val();
        const tanggal_sampai = $('#tanggal_sampai').val();

        $.get('<?= basename(__FILE__) ?>', {
            ajax: '1',
            keyword: keyword,
            tanggal_dari: tanggal_dari,
            tanggal_sampai: tanggal_sampai,
            page: page
        }, function(data) {
            let html = $('<div>').html(data);
            $('#invoice-body').html(html.find('#invoice-body').html() ?? data);
        });
    }

    $('#search').on('keyup', () => fetchData(1));
    $('#tanggal_dari, #tanggal_sampai').on('change', () => fetchData(1));
    $(document).on('click', '.page-link[data-page]', function(e) {
        e.preventDefault();
        fetchData($(this).data('page'));
    });
</script>

</body>

</html>