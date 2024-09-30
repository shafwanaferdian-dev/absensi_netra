<?php
// Meng-include file PHPExcel utama
require 'PHPExcel/Classes/PHPExcel.php';

function getDataDanFilter() {
    // Tentukan direktori data
    $dir = 'data_absensi/';

    // Menentukan bulan yang dipilih
    $bulanTerpilih = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');
    $file = $dir . 'absensi_' . $bulanTerpilih . '.xlsx';

    // Jika file untuk bulan yang dipilih tidak ada, kembalikan pesan error
    if (!file_exists($file)) {
        return [
            'error' => 'File absensi untuk bulan ' . $bulanTerpilih . ' tidak ditemukan.'
        ];
    }

    // Membaca file Excel
    $excelReader = PHPExcel_IOFactory::createReaderForFile($file);
    $excelObj = $excelReader->load($file);
    $sheet = $excelObj->getSheet(0);
    $data = $sheet->toArray(null, true, true, true);

    // Mendapatkan daftar nama unik untuk dropdown filter
    $namaList = [];
    $first = true;
    foreach ($data as $row) {
        if ($first) {
            $first = false;
            continue;
        }
        $namaList[] = $row['B'];
    }
    $namaList = array_unique($namaList);
    sort($namaList);

    // Memfilter data berdasarkan nama yang dipilih
    $namaTerpilih = isset($_GET['nama']) ? $_GET['nama'] : '';
    $filteredData = [];
    foreach ($data as $row) {
        if ($namaTerpilih == '' || $row['B'] == $namaTerpilih) {
            $filteredData[] = $row;
        }
    }

    return [
        'namaList' => $namaList,
        'filteredData' => $filteredData,
        'bulan_ini' => $bulanTerpilih,
        'error' => ''
    ];
}

function getNamaBulanIndonesia($bulan) {
    $daftarBulan = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember'
    ];

    // Ubah format bulan menjadi "MM"
    $bulan = substr($bulan, -2);

    // Periksa apakah kunci tersedia sebelum mengaksesnya
    if (array_key_exists($bulan, $daftarBulan)) {
        return $daftarBulan[$bulan];
    } else {
        return 'Bulan tidak valid'; // atau tindakan yang sesuai
    }
}



function getHariLibur($tahun) {
    // Contoh daftar tanggal merah di Indonesia
    return [
        "$tahun-01-01", // Tahun Baru Masehi
        "$tahun-04-21", // Hari Kartini
        // Tambahkan tanggal merah lainnya di sini
    ];
}

function generateKalender($bulan, $tahun) {
    $hariDalamSeminggu = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $hariPertamaBulan = mktime(0, 0, 0, $bulan, 1, $tahun);
    $jumlahHari = date('t', $hariPertamaBulan);
    $komponenTanggal = getdate($hariPertamaBulan);
    $namaBulan = getNamaBulanIndonesia($bulan);
    $hariDalamSemingguIndonesia = array_map('ucfirst', $hariDalamSeminggu);
    $hariIni = $komponenTanggal['wday'];
    $kalender = "<table class='kalender'>";
    $kalender .= "<caption>$namaBulan $tahun</caption>";
    $kalender .= "<tr>";

    // Menambahkan header hari
    foreach ($hariDalamSemingguIndonesia as $hari) {
        $kalender .= "<th>$hari</th>";
    }
    $kalender .= "</tr><tr>";

    // Jika hari pertama bulan ini bukan Minggu, tambahkan sel kosong
    if ($hariIni > 0) {
        $kalender .= str_repeat('<td></td>', $hariIni);
    }

    $hariSaatIni = 1;
    $hariLibur = getHariLibur($tahun);

    while ($hariSaatIni <= $jumlahHari) {
        if ($hariIni == 7) {
            $hariIni = 0;
            $kalender .= "</tr><tr>";
        }

        $tanggalSaatIni = "$tahun-" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . "-" . str_pad($hariSaatIni, 2, '0', STR_PAD_LEFT);
        if (in_array($tanggalSaatIni, $hariLibur)) {
            $kalender .= "<td class='libur'>$hariSaatIni</td>";
        } else {
            $kalender .= "<td>$hariSaatIni</td>";
        }

        $hariSaatIni++;
        $hariIni++;
    }

    if ($hariIni != 7) {
        $sisaHari = 7 - $hariIni;
        $kalender .= str_repeat('<td></td>', $sisaHari);
    }

    $kalender .= "</tr>";
    $kalender .= "</table>";
    return $kalender;
}
?>