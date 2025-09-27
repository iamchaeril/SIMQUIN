<?php
include 'config.php';

$username = 'admin';
$password_plain = '123456'; // password asli
$password_hashed = password_hash($password_plain, PASSWORD_DEFAULT); // hash password
$name = 'Admin Utama';

$stmt = $conn->prepare("INSERT INTO users (username, password, name) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $password_hashed, $name);

if ($stmt->execute()) {
    echo "User berhasil dibuat.";
} else {
    echo "Gagal: " . $stmt->error;
}

$stmt->close();
$conn->close();
