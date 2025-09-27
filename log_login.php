<?php
function log_login($conn, $username, $status)
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    $agent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';

    $stmt = $conn->prepare("INSERT INTO login_logs (username, status, ip_address, user_agent) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $status, $ip, $agent);
    $stmt->execute();
    $stmt->close();
}
