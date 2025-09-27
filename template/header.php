<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!-- header.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $pageTitle ?? 'SIMQUIN' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .card {
            transition: transform 0.2s;
        }

        .card:hover {
            transform: scale(1.02);
        }

        .navbar-nav .nav-link {
            color: white;
        }

        .navbar-nav .nav-link:hover {
            color: #ffc107;
        }

        .nav-link.active {
            background-color: #ffc107;
            color: white !important;
            border-radius: 0.25rem;
        }

        .content {
            padding: 30px;
        }

        .content-card {
            max-width: 1700px;
            margin: auto;
            background-color: white;
            border-radius: 8px;

        }

        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
        }
    </style>
</head>

<body>