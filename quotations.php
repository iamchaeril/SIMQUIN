<?php

include 'config.php';
function format_rupiah($angka)
{
    return "Rp " . number_format($angka, 0, ',', '.');
}

function get_total_quotation($quotation_id, $conn)
{
    $sql = "SELECT SUM(qty * harga) AS total FROM quotation_items WHERE quotation_id = $quotation_id";
    $result = $conn->query($sql);
    $data = $result->fetch_assoc();
    return $data['total'] ?? 0;
}

function render_pagination($page, $totalPages)
{
    if ($totalPages <= 1) return;

    echo '<tr id="pagination-row"><td colspan="8" class="text-center">
        <nav><ul class="pagination justify-content-center">';

    if ($page > 1) {
        echo '<li class="page-item"><a href="#" class="page-link" data-page="' . ($page - 1) . '">«</a></li>';
    }

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

    if ($page < $totalPages) {
        echo '<li class="page-item"><a href="#" class="page-link" data-page="' . ($page + 1) . '">»</a></li>';
    }

    echo '</ul></nav></td></tr>';
}

if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    $keyword = isset($_GET['keyword']) ? $conn->real_escape_string($_GET['keyword']) : '';
    $tanggal_dari = isset($_GET['tanggal_dari']) ? $_GET['tanggal_dari'] : '';
    $tanggal_sampai = isset($_GET['tanggal_sampai']) ? $_GET['tanggal_sampai'] : '';
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $where = "WHERE 1=1";
    if ($keyword !== '') {
        $where .= " AND (client_name LIKE '%$keyword%' OR nomor LIKE '%$keyword%')";
    }
    if ($tanggal_dari !== '' && $tanggal_sampai !== '') {
        $from = date('Y-m-d', strtotime($tanggal_dari));
        $to = date('Y-m-d', strtotime($tanggal_sampai));
        $where .= " AND DATE(tanggal) BETWEEN '$from' AND '$to'";
    }


    $countSql = "SELECT COUNT(*) as total FROM quotations 
             JOIN clients ON quotations.client_id = clients.id 
             $where";

    $countResult = $conn->query($countSql);
    $totalData = $countResult->fetch_assoc()['total'];
    $totalPages = ceil($totalData / $limit);

    $sql = "SELECT quotations.id, quotations.nomor, quotations.tanggal, quotations.is_approved, clients.client_name, clients.attn, clients.phone, clients.client_address
        FROM quotations
        JOIN clients ON quotations.client_id = clients.id 
        $where
        ORDER BY quotations.id DESC 
        LIMIT $limit OFFSET $offset";
    $result = $conn->query($sql);
    $no = $offset + 1;

    ob_start(); // buffer output
    if ($result->num_rows > 0):
        while ($row = $result->fetch_assoc()):
            $total = get_total_quotation($row['id'], $conn);
            $quotation_id = $row['id'];
            $invoice_sql = "SELECT * FROM invoices WHERE quotation_id = $quotation_id";
            $invoice_result = $conn->query($invoice_sql);
            $invoice_data = $invoice_result->fetch_assoc();
?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['nomor'] ?></td>
                <td><?= $row['client_name'] ?></td>
                <td><?= format_rupiah($total) ?></td>
                <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                <td>
                    <?php if ($row['is_approved'] == 0): ?>
                        <a href="approve_quotation.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">
                            Klik Setuju
                        </a>
                    <?php else: ?>
                        <span class="badge bg-success">Disetujui</span>
                    <?php endif; ?>
                </td>

                <!-- Kolom kedua: Tombol Buat Invoice jika sudah disetujui -->
                <td>
                    <?php if ($row['is_approved'] == 1): ?>
                        <?php if ($invoice_data): ?>
                            <a href="get_invoice.php?id=<?= $invoice_data['id'] ?>" class="btn btn-sm btn-info">
                                Lihat Invoice
                            </a>
                        <?php else: ?>
                            <a href="add_invoice.php?quotation_id=<?= $row['id'] ?>" class="btn btn-sm btn-success">
                                Buat Invoice
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($row['is_approved'] == 0): ?>
                        <a href="get_quotation.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-success">PDF</a>
                        <a href="edit_quotation.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning mb-1">Edit</a>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $row['id'] ?>" data-nomor="<?= htmlspecialchars($row['nomor']) ?>">Delete</button>

                    <?php elseif ($row['is_approved'] == 1): ?>
                        <?php if ($invoice_data): ?>
                            <a href="get_quotation.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-success">PDF</a>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $row['id'] ?>" data-nomor="<?= htmlspecialchars($row['nomor']) ?>">Delete</button>
                        <?php else: ?>
                            <a href="get_quotation.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-success">PDF</a>
                            <a href="edit_quotation.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning mb-1">Edit</a>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $row['id'] ?>" data-nomor="<?= htmlspecialchars($row['nomor']) ?>">Delete</button>
                        <?php endif; ?>
                    <?php endif; ?>

                </td>
            </tr>
        <?php endwhile;
    else: ?>
        <tr>
            <td colspan="8" class="text-center">Data tidak ditemukan</td>
        </tr>
<?php endif;
    render_pagination($page, $totalPages);
    $content = ob_get_clean();
    echo $content;
    exit;
}

$result = $conn->query("SELECT quotations.id, quotations.nomor, quotations.tanggal, quotations.is_approved, clients.client_name, clients.attn, clients.phone, clients.client_address
FROM quotations
JOIN clients ON quotations.client_id = clients.id ORDER BY quotations.id DESC LIMIT 10");
$countSql = "SELECT COUNT(*) as total FROM quotations";
$countResult = $conn->query($countSql);
$totalData = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalData / 10);
?>

<?php
$pageTitle = "Quotations";
$activePage = 'quotations';
include 'template/header.php';
include 'template/navbar.php';
?>

<!-- Main Content -->
<div class="container content py-2">
    <h2 class="text-center" style="margin-top: 0.1rem;">Data Quotations</h2>
    <div class="row align-items-end g-2 mb-3">
        <div class="col-md-2 d-grid">
            <a href="add_quotation.php" class="btn btn-primary h-100">+ Tambah Baru</a>
        </div>

        <div class="col-md-4">
            <input type="text" id="search" class="form-control" placeholder="Cari client atau nomor quotation...">
        </div>

        <div class="col-md-2">
            <input type="date" id="tanggal_dari" class="form-control">
        </div>

        <div class="col-auto d-flex align-items-center px-1">
            <span class="fw-bold">s/d</span>
        </div>

        <div class="col-md-2">
            <input type="date" id="tanggal_sampai" class="form-control">
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nomor</th>
                <th>Client</th>
                <th>Nilai</th>
                <th>Tanggal</th>
                <th>Respon Client</th>
                <th>Invoice</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="quotation-body">
            <?php
            $no = 1;
            while ($row = $result->fetch_assoc()):
                $total = get_total_quotation($row['id'], $conn);
                $client_id = $row['id'];
                $client_sql = "SELECT * FROM clients WHERE client_id = $client_id";
                $quotation_id = $row['id'];
                $invoice_sql = "SELECT * FROM invoices WHERE quotation_id = $quotation_id";
                $invoice_result = $conn->query($invoice_sql);
                $invoice_data = $invoice_result->fetch_assoc();
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['nomor'] ?></td>
                    <td><?= $row['client_name'] ?></td>
                    <td><?= format_rupiah($total) ?></td>
                    <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                    <td>
                        <?php if ($row['is_approved'] == 0): ?>
                            <a href="approve_quotation.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">
                                Klik Setuju
                            </a>
                        <?php else: ?>
                            <span class="badge bg-success">Disetujui</span>
                        <?php endif; ?>
                    </td>

                    <!-- Kolom kedua: Tombol Buat Invoice jika sudah disetujui -->
                    <td>
                        <?php if ($row['is_approved'] == 1): ?>
                            <?php if ($invoice_data): ?>
                                <a href="get_invoice.php?id=<?= $invoice_data['id'] ?>" class="btn btn-sm btn-info">
                                    Lihat Invoice
                                </a>
                            <?php else: ?>
                                <a href="add_invoice.php?quotation_id=<?= $row['id'] ?>" class="btn btn-sm btn-success">
                                    Buat Invoice
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>

                    </td>
                    <td>
                        <?php if ($row['is_approved'] == 0): ?>
                            <a href="get_quotation.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-success">PDF</a>
                            <a href="edit_quotation.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning mb-1">Edit</a>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $row['id'] ?>" data-nomor="<?= htmlspecialchars($row['nomor']) ?>">Delete</button>

                        <?php elseif ($row['is_approved'] == 1): ?>
                            <?php if ($invoice_data): ?>
                                <a href="get_quotation.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-success">PDF</a>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $row['id'] ?>" data-nomor="<?= htmlspecialchars($row['nomor']) ?>">Delete</button>
                            <?php else: ?>
                                <a href="get_quotation.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-success">PDF</a>
                                <a href="edit_quotation.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning mb-1">Edit</a>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $row['id'] ?>" data-nomor="<?= htmlspecialchars($row['nomor']) ?>">Delete</button>
                            <?php endif; ?>
                        <?php endif; ?>

                    </td>
                </tr>
            <?php endwhile ?>
            <?php render_pagination(1, $totalPages); ?>
        </tbody>
    </table>
</div>

<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Quotation berhasil ditambahkan.',
            showConfirmButton: false,
            timer: 2000
        });
    </script>
<?php endif; ?>

<?php if (isset($_GET['success']) && $_GET['success'] == 2): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Quotation berhasil disetujui.',
            showConfirmButton: false,
            timer: 2000
        });
    </script>
<?php endif; ?>

<?php include 'template/scriptjs.php'; ?>

<script>
    let currentPage = 1;

    function fetchData(page = 1) {
        currentPage = page;
        let keyword = $('#search').val();
        let tanggal_dari = $('#tanggal_dari').val();
        let tanggal_sampai = $('#tanggal_sampai').val();

        $.get('<?= basename(__FILE__) ?>', {
            ajax: '1',
            keyword: keyword,
            tanggal_dari: tanggal_dari,
            tanggal_sampai: tanggal_sampai,
            page: page
        }, function(data) {
            let html = $('<div>').html(data);
            $('#quotation-body').html(html.find('#quotation-body').html() ?? data);
        });
    }

    $('#search').on('keyup', () => fetchData(1));
    $('#tanggal_dari, #tanggal_sampai').on('change', () => fetchData(1));

    $(document).on('click', '.page-link[data-page]', function(e) {
        e.preventDefault();
        fetchData($(this).data('page'));
    });
</script>

<script>
    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        const nomor = $(this).data('nomor');

        Swal.fire({
            title: 'Yakin ingin menghapus?',
            html: `Quotation <strong>${nomor}</strong> akan dihapus permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('delete_quotation.php', {
                    id: id
                }, function(response) {
                    if (response === 'OK') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: `Quotation ${nomor} telah dihapus.`,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        fetchData(currentPage); // reload current page
                    } else {
                        Swal.fire('Gagal', 'Gagal menghapus data.', 'error');
                    }
                });
            }
        });
    });
</script>



</body>

</html>