<?php
include 'config.php';

// Ambil data dari form dengan sanitasi dasar
$client = $_POST['client_name'] ?? '';
$alamat = $_POST['client_address'] ?? '';
$phone = $_POST['phone'] ?? '';
$attn = $_POST['attn'] ?? '';
$email = $_POST['email'] ?? '';

// Siapkan statement SQL dengan placeholder
$stmt = $conn->prepare("INSERT INTO clients (client_name, client_address, phone, attn, email) VALUES (?, ?, ?, ?, ?)");

// Cek jika prepare berhasil
if ($stmt) {
    // Binding parameter ke statement: s = string
    $stmt->bind_param("sssss", $client, $alamat, $phone, $attn, $email);

    // Eksekusi statement
    if ($stmt->execute()) {
        header("Location: clients.php?success=1");
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
