<?php
include 'config.php';

// Ambil data dari form dengan sanitasi dasar
$nama_produk = $_POST['product_name'] ?? '';
$kategori_produk = $_POST['category'] ?? '';
$harga = $_POST['price'] ?? '';

// Siapkan statement SQL dengan placeholder
$stmt = $conn->prepare("INSERT INTO products (product_name, category, price) VALUES (?, ?, ?)");

// Cek jika prepare berhasil
if ($stmt) {
    // Binding parameter ke statement: s = string
    $stmt->bind_param("sss", $nama_produk, $kategori_produk, $harga);

    // Eksekusi statement
    if ($stmt->execute()) {
        header("Location: products.php?success=1");
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
