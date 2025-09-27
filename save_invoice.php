<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $quotation_id    = $_POST['quotation_id'];
    $invoice_number  = $_POST['invoice_number'];
    $invoice_date    = $_POST['invoice_date'];

    // Insert ke tabel invoices
    $stmt = $conn->prepare("INSERT INTO invoices (quotation_id, invoice_number, invoice_date) VALUES (?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed (invoices): " . $conn->error);
    }

    $stmt->bind_param("iss", $quotation_id, $invoice_number, $invoice_date);
    if (!$stmt->execute()) {
        die("Execute failed (invoices): " . $stmt->error);
    }

    $invoice_id = $stmt->insert_id;
    $stmt->close();

    // Insert item-item ke invoice_items
    if (isset($_POST['deskripsi'], $_POST['qty'], $_POST['harga'])) {
        $deskripsi = $_POST['deskripsi'];
        $qty       = $_POST['qty'];
        $harga     = $_POST['harga'];

        $stmt_item = $conn->prepare("INSERT INTO invoice_items (invoice_id, deskripsi, qty, harga) VALUES (?, ?, ?, ?)");
        if (!$stmt_item) {
            die("Prepare failed (invoice_items): " . $conn->error);
        }

        for ($i = 0; $i < count($deskripsi); $i++) {
            $desc_item  = $deskripsi[$i];
            $qty_item   = $qty[$i];
            $harga_item = $harga[$i];

            if ($desc_item !== '' && $qty_item !== '' && $harga_item !== '') {
                $stmt_item->bind_param("isii", $invoice_id, $desc_item, $qty_item, $harga_item);
                if (!$stmt_item->execute()) {
                    die("Execute failed (invoice_items) on item $i: " . $stmt_item->error);
                }
            }
        }

        $stmt_item->close();
    }

    header("Location: invoices.php?success=1");
    exit;
} else {
    echo "Invalid request.";
}
