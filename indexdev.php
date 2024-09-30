<?php
session_start();
include('db.php'); // Koneksi ke database

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Fungsi untuk mengambil data absensi dari database berdasarkan periode 21 Agustus - 20 September
function getDataFromDatabase($nama, $bulan, $tahun) {
    global $db;

    // Periode tanggal berdasarkan bulan
    $start_date = "$tahun-08-21"; // Mulai dari 21 Agustus
    $end_date = "$tahun-09-20";   // Hingga 20 September

    // Query SQL untuk mengambil data absensi
    $query = "SELECT id_staff AS No_ID, nama_staff AS Nama, tanggal_absen AS Tanggal, scan_masuk AS ScanMasuk, scan_pulang AS ScanPulang
              FROM data_absensi 
              WHERE nama_staff = :nama AND tanggal_absen BETWEEN :start_date AND :end_date 
              ORDER BY tanggal_absen";
              
    $stmt = $db->prepare($query);
    $stmt->execute([':nama' => $nama, ':start_date' => $start_date, ':end_date' => $end_date]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Ambil nama dari filter (GET request)
$nama = isset($_GET['nama']) ? $_GET['nama'] : $_SESSION['username'];

// Ambil bulan dan tahun dari filter (GET request)
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Ambil data absensi dari database
$filteredData = getDataFromDatabase($nama, $bulan, $tahun);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Staff Netra Tangerang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2a60b;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>

<h2>Absensi Staff Netra Tangerang - September <?php echo $tahun; ?></h2>

<p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! | <a href="logout.php">Logout</a> | <a href="reset_password.php">Reset Password</a></p>

<!-- Form Filter -->
<form method="GET" action="index.php">
    Pilih Bulan:
    <select name="bulan">
        <option value="09" <?php echo ($bulan == '09') ? 'selected' : ''; ?>>September</option>
    </select>
    Filter Nama:
    <select name="nama">
        <option value="Shafwana Ferdian Akbar" <?php echo ($nama == 'Shafwana Ferdian Akbar') ? 'selected' : ''; ?>>Shafwana Ferdian Akbar</option>
        <!-- Tambahkan nama lainnya jika diperlukan -->
    </select>
    <input type="submit" value="Filter">
</form>

<!-- Tabel Data Absensi -->
<table>
    <thead>
        <tr>
            <th>No. ID</th>
            <th>Nama</th>
            <th>Tanggal</th>
            <th>Scan Masuk</th>
            <th>Scan Pulang</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($filteredData)): ?>
            <?php foreach ($filteredData as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['No_ID']); ?></td>
                    <td><?php echo htmlspecialchars($row['Nama']); ?></td>
                    <td><?php echo htmlspecialchars($row['Tanggal']); ?></td>
                    <td><?php echo htmlspecialchars($row['ScanMasuk']); ?></td>
                    <td><?php echo htmlspecialchars($row['ScanPulang']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">Data tidak ditemukan</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
