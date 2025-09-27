<?php
include 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$data = null;
$items = [];

// Ambil data quotation dan client dengan prepared statement
$stmt = $conn->prepare(
    "SELECT quotations.id, quotations.nomor, quotations.tanggal, quotations.is_approved,
            clients.client_name, clients.attn, clients.phone, clients.client_address
     FROM quotations
     JOIN clients ON quotations.client_id = clients.id
     WHERE quotations.id = ?"
);

if ($stmt) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
}

// Ambil item quotation dengan prepared statement
$stmt2 = $conn->prepare("SELECT * FROM quotation_items WHERE quotation_id = ?");
if ($stmt2) {
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    while ($row = $result2->fetch_assoc()) {
        $items[] = $row;
    }
    $stmt2->close();
}

// Lanjutkan ke proses generate PDF
include 'generate_quotation_pdf.php';
