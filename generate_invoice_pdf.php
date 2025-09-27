<?php
require 'fpdf/fpdf.php';

$pdf = new FPDF();
$pdf->AddPage();

// ==== Logo ====
$pdf->Image('logo.png', 10, 15, 70); // Pastikan file logo.png ada di folder yang sama

// ==== Info perusahaan di kanan logo ====
$pdf->SetXY(85, 10);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, 'PT JASA RAYA INFORMATIKA', 0, 1);

$pdf->SetFont('Arial', '', 10);
$pdf->SetX(85);
$pdf->Cell(0, 6, 'Jl. Merdeka No. 10, Jakarta', 0, 1);
$pdf->SetX(85);
$pdf->Cell(0, 6, 'Telp: (021) 12345678 | Email : info@jasarayainformatika.com', 0, 1);

$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'INVOICE', 0, 1, 'C');

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(100, 6, 'Tanggal: ' . date('d-m-Y', strtotime($data['invoice_date'])), 0, 1);
$pdf->Cell(100, 6, 'No Invoice: ' . $data['invoice_number'], 0, 1);
$pdf->Cell(100, 6, 'No Referensi: ' . $data['quotation_nomor'], 0, 1);



$pdf->Ln(5);
$pdf->Cell(100, 6, 'Client: ' . $data['client_name'], 0, 1);
$pdf->Cell(100, 6, 'Attn.: ' . $data['attn'], 0, 1);
$pdf->Cell(100, 6, 'Telp: ' . $data['phone'], 0, 1);
$pdf->Cell(100, 6, 'Alamat: ' . $data['client_address'], 0, 1);

$pdf->Ln(5);

// Header table
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(80, 8, 'Deskripsi', 1);
$pdf->Cell(30, 8, 'Qty', 1);
$pdf->Cell(40, 8, 'Harga', 1);
$pdf->Cell(40, 8, 'Subtotal', 1);
$pdf->Ln();

// Data table
$pdf->SetFont('Arial', '', 12);
$total = 0;
foreach ($items as $item) {
    $subtotal = $item['qty'] * $item['harga'];
    $total += $subtotal;
    $pdf->Cell(80, 8, $item['deskripsi'], 1);
    $pdf->Cell(30, 8, $item['qty'], 1, 0, 'R');
    $pdf->Cell(40, 8, number_format($item['harga'], 0, ',', '.'), 1, 0, 'R');
    $pdf->Cell(40, 8, number_format($subtotal, 0, ',', '.'), 1, 0, 'R');
    $pdf->Ln();
}

// Total
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(150, 8, 'Total', 1);
$pdf->Cell(40, 8, number_format($total, 0, ',', '.'), 1, 0, 'R');

$pdf->Ln(12); // Spasi setelah total

$pdf->SetFont('Arial', 'B', 12);
$status_pembayaran = ($data['is_paid'] == 1) ? 'LUNAS' : 'BELUM LUNAS';

// Tambahkan kotak status pembayaran
$pdf->SetTextColor(255); // Warna teks putih
$pdf->SetFillColor(($data['is_paid'] == 1) ? 0 : 255, ($data['is_paid'] == 1) ? 153 : 0, 0); // Hijau untuk lunas, merah untuk belum
$pdf->Cell(75, 10, 'Status Pembayaran: ' . $status_pembayaran, 0, 1, 'l', true);

// Reset warna ke default
$pdf->SetTextColor(0);

$pdf->Output("I", "Invoice_" . $data['invoice_number'] . ".pdf");
