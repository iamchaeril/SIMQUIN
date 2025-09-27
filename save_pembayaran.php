<?php
include 'config.php';

// Ambil data dari form dengan sanitasi dasar
$invoice_id = $_POST['invoice_id'] ?? '';
$payment_date = $_POST['payment_date'] ?? '';
$amount_paid = $_POST['amount_paid'] ?? '';
$payment_method = $_POST['payment_method'] ?? '';
$notes = $_POST['notes'] ?? '';

// Siapkan statement SQL dengan placeholder
$stmt = $conn->prepare("INSERT INTO invoice_payments (invoice_id, payment_date, amount_paid, payment_method, notes) VALUES (?, ?, ?, ?, ?)");

// Cek jika prepare berhasil
if ($stmt) {
    // Binding parameter ke statement: s = string
    $stmt->bind_param("sssss", $invoice_id, $payment_date, $amount_paid, $payment_method, $notes);

    // Eksekusi statement
    if ($stmt->execute()) {
        // Setelah berhasil insert, update is_paid di tabel invoices
        $update_stmt = $conn->prepare("UPDATE invoices SET is_paid = 1 WHERE id = ?");
        if ($update_stmt) {
            $update_stmt->bind_param("s", $invoice_id);
            $update_stmt->execute();
            $update_stmt->close();
        }
        header("Location: invoices.php?success=1");
        exit;
    } else {
        // Jika gagal insert
        echo "Gagal menyimpan data: " . $stmt->error;
    }

    $stmt->close();
} else {
    // Jika prepare gagal
    echo "Gagal mempersiapkan statement: " . $conn->error;
}
