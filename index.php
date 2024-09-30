<?php
session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit();
}

// Ambil nama staf dari sesi
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

if (!file_exists('data_processing.php')) {
    die('File data_processing.php tidak ditemukan.');
}

require 'data_processing.php';
$result = getDataDanFilter();
$namaList = isset($result['namaList']) ? $result['namaList'] : [];
$filteredData = isset($result['filteredData']) ? $result['filteredData'] : [];
$bulan_ini = isset($result['bulan_ini']) ? $result['bulan_ini'] : '';
$error = isset($result['error']) ? $result['error'] : '';
$bulanDanTahun = explode('-', $bulan_ini);

if (count($bulanDanTahun) >= 2) {
    $bulan = (int)$bulanDanTahun[1];
    $tahun = (int)$bulanDanTahun[0];
} else {
    $bulan = date('m');
    $tahun = date('Y');
}

$calendar = generateKalender($bulan, $tahun);

if ($error) {
    echo "<script>alert('$error');</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Absensi</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('namaFilter').onchange = function() {
                document.getElementById('namaTerpilih').value = this.value;
            };
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectElement = document.getElementById('namaTerpilih');

            // Mengambil nilai yang tersimpan di localStorage saat halaman dimuat
            const savedSelection = localStorage.getItem('selectedNama');
            if (savedSelection) {
                selectElement.value = savedSelection;
            }

            // Menyimpan nilai yang dipilih ke localStorage saat pilihan berubah
            selectElement.addEventListener('change', function() {
                localStorage.setItem('selectedNama', this.value);
            });
        });
    </script>
</head>
<body>
    <h1>Absensi Staff Netra Tangerang - <?= getNamaBulanIndonesia($bulan_ini) ?> <?= $bulanDanTahun[0] ?></h1>

    <div id="filter-form">
        <p>Welcome, <?= htmlspecialchars($username) ?>! || 
            <a href="reset_password.php" class="reset-password-button">Reset Password</a>
        </p> <!-- Menampilkan nama staf -->
        
        <form method="GET" action="">
            <label for="bulan">Pilih Bulan:</label>
            <select name="bulan" id="bulan">
                <?php
                for ($month = 1; $month <= 12; $month++) {
                    $monthStr = date('Y-') . str_pad($month, 2, '0', STR_PAD_LEFT);
                    $monthName = getNamaBulanIndonesia($monthStr);
                    echo '<option value="' . $monthStr . '" ' . ($bulan_ini == $monthStr ? 'selected' : '') . '>' . $monthName . '</option>';
                }
                ?>
            </select>
            
            <label for="nama">Filter Nama:</label>
            <select name="nama" id="namaFilter">
                <option value="">Semua</option>
                <?php
                foreach ($namaList as $nama) {
                    echo '<option value="' . $nama . '" ' . (isset($_GET['nama']) && $_GET['nama'] == $nama ? 'selected' : '') . '>' . $nama . '</option>';
                }
                ?>
            </select>
            <button type="submit">Filter</button>
        </form>

        <form method="POST" action="logout.php" style="display:inline;">
            <button type="submit">Logout</button>
        </form>

        <form method="POST" action="export_pdf.php" style="display:inline;">
            <input type="hidden" name="bulan_ini" value="<?= htmlspecialchars($bulan_ini) ?>">
            <input type="hidden" name="namaTerpilih" value="<?= isset($_GET['nama']) ? htmlspecialchars($_GET['nama']) : '' ?>">

            <?php if (!isset($_GET['nama'])): ?>
                <select name="namaTerpilih" id="namaTerpilih">
                    <option value="">Semua</option>
                    <?php
                    foreach ($namaList as $nama) {
                        $selected = (isset($_GET['nama']) && $_GET['nama'] == $nama) ? 'selected' : '';
                        echo '<option value="' . $nama . '" ' . $selected . '>' . $nama . '</option>';
                    }
                    ?>
                </select>
            <?php else: ?>
                <input type="hidden" name="namaTerpilih" value="<?= $_GET['nama']; ?>">
            <?php endif; ?>

            <button type="submit">Export to PDF</button>
        </form>
    </div>

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
            <?php
            $first = true;
            foreach ($filteredData as $row) {
                if ($first) {
                    $first = false;
                    continue;
                }
                $tanggal = $row['C']; // mengasumsikan tanggal ada di kolom C
                $hariDalamMinggu = date('l', strtotime($tanggal)); // dapatkan nama hari dalam minggu (misalnya Senin, Selasa, dll.)
                echo '<tr>';
                echo '<td>' . $row['A'] . '</td>';
                echo '<td>' . $row['B'] . '</td>';
                echo '<td>' . $tanggal . ' (' . $hariDalamMinggu . ')' . '</td>'; // tampilkan tanggal dengan nama hari dalam minggu
                echo '<td>' . $row['D'] . '</td>';
                echo '<td>' . $row['E'] . '</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</body>
</html>