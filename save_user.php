<?php
include 'config.php';

// Ambil data dari form dengan sanitasi dasar
$nama_user = $_POST['name'] ?? '';
$user_name = $_POST['username'] ?? '';
$pass = $_POST['password'] ?? '';
$password_hashed = password_hash($pass, PASSWORD_DEFAULT); // hash password
$level_user = $_POST['level'] ?? '';

// Siapkan statement SQL dengan placeholder
$stmt = $conn->prepare("INSERT INTO users (name, username, password, level) VALUES (?, ?, ?, ?)");

// Cek jika prepare berhasil
if ($stmt) {
    // Binding parameter ke statement: s = string
    $stmt->bind_param("ssss", $nama_user, $user_name, $password_hashed, $level_user);

    // Eksekusi statement
    if ($stmt->execute()) {
        header("Location: users.php?success=1");
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
