<?php
include 'config.php';
$pageTitle = "Dashboard";
$activePage = 'dashboard';
include 'template/header.php';
include 'template/navbar.php';

// Ambil data count dari masing-masing tabel
function getCount($conn, $table)
{
    $result = $conn->query("SELECT COUNT(*) AS total FROM $table");
    $row = $result->fetch_assoc();
    return $row['total'];
}

function getCountByIntStatus($conn, $table, $column, $value)
{
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM $table WHERE $column = ?");
    $stmt->bind_param("i", $value);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'];
}


$clientCount = getCount($conn, 'clients');
$quotationCount = getCount($conn, 'quotations');
$quotationItemCount = getCount($conn, 'quotation_items');
$invoiceCount = getCount($conn, 'invoices');
$invoiceItemCount = getCount($conn, 'invoice_items');

$invoicePaid = getCountByIntStatus($conn, 'invoices', 'is_paid', 1);
$invoiceUnpaid = getCountByIntStatus($conn, 'invoices', 'is_paid', 0);
$quotationApproved = getCountByIntStatus($conn, 'quotations', 'is_approved', 1);
$quotationPending = getCountByIntStatus($conn, 'quotations', 'is_approved', 0);

?>

<div class="container content py-2">
    <div class="container mt-5">
        <h2 class="text-center" style="margin-top: 0.1rem;">Dashboard</h2>
        <div class="row g-4">
            <div class="col-sm-4">
                <div class="card shadow-sm border-start border-primary border-4">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-users text-primary me-2"></i> Clients</h5>
                        <p class="card-text fs-4"><?= $clientCount ?></p>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card shadow-sm border-start border-secondary border-4">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-file-invoice-dollar text-success me-2"></i> Quotations <b><?= $quotationCount ?></b></h5>
                        <p class="card-text fs-4"> <i class="fas fa-check-circle text-primary me-2"></i><?= $quotationApproved ?> <i class="fas fa-clock text-warning me-2"></i><?= $quotationPending ?></p>

                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card shadow-sm border-start border-info border-4">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-receipt text-danger me-2"></i> Invoices <b><?= $invoiceCount ?></b></h5>
                        <p class="card-text fs-4"> <i class="fas fa-money-check-alt text-success me-2"></i><?= $invoicePaid ?> <i class="fas fa-exclamation-circle text-danger me-2"></i><?= $invoiceUnpaid ?></p>
                    </div>
                </div>
            </div>




        </div>
    </div>


</div>

<?php include 'template/scriptjs.php'; ?>

</body>

</html>