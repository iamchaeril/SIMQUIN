<?php
include 'config.php'; // koneksi ke database

// Ambil nama user berdasarkan session user_id
include 'config.php'; // koneksi ke database

$nama_user = 'Guest';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Gunakan prepared statement
    $stmt = mysqli_prepare($conn, "SELECT name FROM users WHERE id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $user_id); // "i" untuk integer
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $nama);
        if (mysqli_stmt_fetch($stmt)) {
            $nama_user = $nama;
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!-- navbar.php -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">SIMQUIN</a>
        <span class="text-warning ms-3 fs-6">Hallo, <?= htmlspecialchars($nama_user) ?></span>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    <a class="nav-link <?= ($activePage == 'dashboard') ? 'active' : '' ?>" href="index.php">
                        <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activePage == 'clients') ? 'active' : '' ?>" href="clients.php">
                        <i class="fas fa-users me-1"></i> Clients
                    </a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link <?= ($activePage == 'products') ? 'active' : '' ?>" href="products.php">
                        <i class="fas fa-tags me-1"></i> Product
                    </a>
                </li> -->
                <li class="nav-item">
                    <a class="nav-link <?= ($activePage == 'quotations') ? 'active' : '' ?>" href="quotations.php">
                        <i class="fas fa-file-invoice me-1"></i> Quotation
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activePage == 'invoices') ? 'active' : '' ?>" href="invoices.php">
                        <i class="fas fa-receipt me-1"></i> Invoice
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activePage == 'pembayaran') ? 'active' : '' ?>" href="pembayaran.php">
                        <i class="fas fa-file-invoice-dollar me-1"></i> Laporan Pembayaran
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activePage == 'users') ? 'active' : '' ?>" href="users.php">
                        <i class="fas fa-user-group me-1"></i> Users
                    </a>
                </li>


                <li class="nav-item"><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-1"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>