<?php
require('fpdf/fpdf.php');

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


// ==== Judul Quotation ====
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'QUOTATION', 0, 1, 'C');

$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(30, 7, "Nomor:", 0);
$pdf->SetFont('Arial', '', 11);
$pdf->SetX(30); // indent untuk alamat
$pdf->Cell(0, 7, $data['nomor'], 0, 1);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(30, 7, "Tanggal:", 0);
$pdf->SetFont('Arial', '', 11);
$pdf->SetX(30); // indent untuk alamat
$pdf->Cell(0, 7, $data['tanggal'], 0, 1);

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(30, 7, "Untuk:", 0);
$pdf->SetFont('Arial', '', 11);
$pdf->SetX(30); // indent untuk alamat
$pdf->Cell(0, 7, $data['client_name'], 0, 1);

$pdf->SetX(30); // indent untuk alamat
$pdf->MultiCell(0, 6, $data['client_address'], 0);
$pdf->SetX(30); // indentasi
$pdf->MultiCell(0, 6,  $data['attn'] . ' / ' . $data['phone'], 0);


$pdf->Ln(5);

//tabel
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(10, 8, 'No', 1, 0, 'C');
$pdf->Cell(80, 8, 'Deskripsi', 1, 0, 'C');
$pdf->Cell(20, 8, 'Qty', 1, 0, 'C');
$pdf->Cell(40, 8, 'Harga', 1, 0, 'C');
$pdf->Cell(40, 8, 'Subtotal', 1, 1, 'C');


$pdf->SetFont('Arial', '', 11);
$total = 0;
foreach ($items as $i => $item) {
    $subtotal = $item['qty'] * $item['harga'];
    $total += $subtotal;
    $pdf->Cell(10, 8, $i + 1, 1);
    $pdf->Cell(80, 8, $item['deskripsi'], 1);
    $pdf->Cell(20, 8, $item['qty'], 1, 0, 'C'); // Qty rata tengah
    $pdf->Cell(40, 8, "Rp" . number_format($item['harga'], 0, ',', '.'), 1, 0, 'R'); // Harga rata kanan
    $pdf->Cell(40, 8, "Rp" . number_format($subtotal, 0, ',', '.'), 1, 1, 'R'); // Subtotal rata kanan & pindah baris

    $pdf->Ln();
}

// Total

$pdf->Cell(150, 8, 'Total', 1, 0, 'R');
$pdf->Cell(40, 8, "Rp" . number_format($total, 0, ',', '.'), 1, 0, 'R');

$pdf->Output('I', 'quotation_' . $data['nomor'] . '.pdf');
