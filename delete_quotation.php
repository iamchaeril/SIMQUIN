<?php
include 'config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']); // quotation ID

    // Ambil invoice_id dari quotation ini
    $stmt_get_invoice = $conn->prepare("SELECT id FROM invoices WHERE quotation_id = ?");
    $stmt_get_invoice->bind_param("i", $id);
    $stmt_get_invoice->execute();
    $result_invoice = $stmt_get_invoice->get_result();
    $invoice_id = null;
    if ($row = $result_invoice->fetch_assoc()) {
        $invoice_id = $row['id'];
    }
    $stmt_get_invoice->close();


    // Hapus invoice payment (jika ada)
    $success_invoice = true;
    if ($invoice_id !== null) {
        $stmt_invoice = $conn->prepare("DELETE FROM invoice_payments WHERE invoice_id = ?");
        $stmt_invoice->bind_param("i", $invoice_id);
        $success_invoice = $stmt_invoice->execute();
        $stmt_invoice->close();
    }
    // Hapus invoice_items kalau ada invoice terkait
    $success_invoice_items = true;
    if ($invoice_id !== null) {
        $stmt_invoice_items = $conn->prepare("DELETE FROM invoice_items WHERE invoice_id = ?");
        $stmt_invoice_items->bind_param("i", $invoice_id);
        $success_invoice_items = $stmt_invoice_items->execute();
        $stmt_invoice_items->close();
    }

    // Hapus invoice (jika ada)
    $success_invoice = true;
    if ($invoice_id !== null) {
        $stmt_invoice = $conn->prepare("DELETE FROM invoices WHERE id = ?");
        $stmt_invoice->bind_param("i", $invoice_id);
        $success_invoice = $stmt_invoice->execute();
        $stmt_invoice->close();
    }

    // Hapus quotation_items
    $stmt_items = $conn->prepare("DELETE FROM quotation_items WHERE quotation_id = ?");
    $stmt_items->bind_param("i", $id);
    $success_items = $stmt_items->execute();
    $stmt_items->close();

    // Hapus quotation
    $stmt_quotation = $conn->prepare("DELETE FROM quotations WHERE id = ?");
    $stmt_quotation->bind_param("i", $id);
    $success_quotation = $stmt_quotation->execute();
    $stmt_quotation->close();

    // Cek apakah semua berhasil
    if ($success_invoice_items && $success_invoice && $success_items && $success_quotation) {
        echo 'OK';
    } else {
        echo 'ERROR';
    }
} else {
    echo 'INVALID';
}
