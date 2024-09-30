<?php
require 'fpdf.php'; // Pastikan path ini benar
require 'data_processing.php'; // Mengambil data dan fungsi dari file data_processing.php

// Periksa apakah user sudah login
session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit();
}

// Ambil parameter filter dari POST
$bulan_ini = isset($_POST['bulan_ini']) ? $_POST['bulan_ini'] : '';
$namaTerpilih = isset($_POST['namaTerpilih']) ? $_POST['namaTerpilih'] : '';

// Ambil data terfilter dari data_processing.php
$result = getDataDanFilter($bulan_ini, $namaTerpilih);  // Pastikan fungsi ini mendukung filter nama dan bulan
$filteredData = isset($result['filteredData']) ? $result['filteredData'] : [];
$bulanDanTahun = explode('-', $bulan_ini);
$bulan = (int)$bulanDanTahun[1];
$tahun = (int)$bulanDanTahun[0];

// Filter data berdasarkan staff yang terpilih
$filteredData = array_filter($filteredData, function($row) use ($namaTerpilih) {
    return $row['B'] == $namaTerpilih;
});

// Buat PDF hanya dengan data terfilter
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10); // Ukuran font 10

// Title
$pdf->Cell(0, 10, 'Absensi Staff Netra Tangerang - ' . getNamaBulanIndonesia($bulan_ini) . ' ' . $tahun, 0, 1, 'C');
$pdf->Ln(10);

// Nama Staff
$pdf->Cell(0, 8, 'Nama Staff: ' . $namaTerpilih, 0, 1, 'L');
$pdf->Ln(10);

// Table Header
$pdf->SetFont('Arial', 'B', 10); // Ukuran font 10
$pdf->Cell(25, 8, 'No. ID', 1);
$pdf->Cell(50, 8, 'Nama', 1);
$pdf->Cell(40, 8, 'Tanggal', 1);
$pdf->Cell(30, 8, 'Scan Masuk', 1);
$pdf->Cell(30, 8, 'Scan Pulang', 1);
$pdf->Ln();

// Table Data - Hanya menampilkan data terfilter
$pdf->SetFont('Arial', '', 10); // Ukuran font 10
foreach ($filteredData as $row) {
    $pdf->Cell(25, 8, $row['A'], 1);
    $pdf->Cell(50, 8, $row['B'], 1);
    $pdf->Cell(40, 8, $row['C'], 1);
    $pdf->Cell(30, 8, $row['D'], 1);
    $pdf->Cell(30, 8, $row['E'], 1);
    $pdf->Ln();
}

// Output PDF
ob_end_clean(); // Hapus output buffer
$pdf->Output('D', 'Data Absensi ' . $namaTerpilih . '-' . getNamaBulanIndonesia($bulan_ini) . $tahun . '.pdf');
exit();