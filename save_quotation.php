<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nomor     = $_POST['nomor'];
    $tanggal   = $_POST['tanggal'];
    $client_id = $_POST['client_id'];

    // Insert ke tabel quotations
    $stmt = $conn->prepare("INSERT INTO quotations (client_id, nomor, tanggal) VALUES (?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed (quotations): " . $conn->error);
    }

    $stmt->bind_param("iss", $client_id, $nomor, $tanggal);
    if (!$stmt->execute()) {
        die("Execute failed (quotations): " . $stmt->error);
    }

    // Ambil quotation_id yang baru saja dibuat
    $quotation_id = $stmt->insert_id;

    // Insert item-item ke quotation_items
    if (isset($_POST['deskripsi'], $_POST['qty'], $_POST['harga'])) {
        $deskripsi_arr = $_POST['deskripsi'];
        $qty_arr       = $_POST['qty'];
        $harga_arr     = $_POST['harga'];

        $stmt_item = $conn->prepare("INSERT INTO quotation_items (quotation_id, deskripsi, qty, harga) VALUES (?, ?, ?, ?)");
        if (!$stmt_item) {
            die("Prepare failed (quotation_items): " . $conn->error);
        }

        for ($i = 0; $i < count($deskripsi_arr); $i++) {
            $desc  = $deskripsi_arr[$i];
            $qty   = $qty_arr[$i];
            $harga = $harga_arr[$i];

            if ($desc !== '' && $qty !== '' && $harga !== '') {
                $stmt_item->bind_param("isii", $quotation_id, $desc, $qty, $harga);
                if (!$stmt_item->execute()) {
                    die("Execute failed (quotation_items) on item $i: " . $stmt_item->error);
                }
            }
        }
    }

    header("Location: quotations.php?success=1");
    exit;
} else {
    echo "Invalid request.";
}
