<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id); // i = integer
        if ($stmt->execute()) {
            echo 'OK';
        } else {
            echo 'ERROR: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        echo 'ERROR: ' . $conn->error;
    }
}
