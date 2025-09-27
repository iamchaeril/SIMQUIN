<?php
include 'config.php';

$id = $_GET['id'] ?? 0;

if ($id) {
    $stmt = $conn->prepare("UPDATE quotations SET is_approved = 1 WHERE id = ?");
    $stmt->bind_param("i", $id); // "i" = integer
    $stmt->execute();
    $stmt->close();
}

header("Location: quotations.php?success=2");
exit;
