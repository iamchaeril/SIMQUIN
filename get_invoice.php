<?php
include 'config.php';

$id = $_GET['id'] ?? 0;

$data = null;
$items = [];

if ($stmt = $conn->prepare("SELECT invoices.*, quotations.nomor AS quotation_nomor, quotations.tanggal AS quotation_tanggal, clients.client_name, clients.attn, clients.phone, clients.client_address
                            FROM invoices
                            JOIN quotations ON invoices.quotation_id = quotations.id
                            JOIN clients ON quotations.client_id = clients.id
                            WHERE invoices.id = ?")) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
}

// Ambil item invoice dengan prepared statement juga
if ($stmt2 = $conn->prepare("SELECT * FROM invoice_items WHERE invoice_id = ?")) {
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    while ($row = $result2->fetch_assoc()) {
        $items[] = $row;
    }
    $stmt2->close();
}

include 'generate_invoice_pdf.php';
