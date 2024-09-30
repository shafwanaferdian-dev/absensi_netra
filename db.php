<?php
// Seting database
$host = 'localhost'; // Host database
$dbname = 'absensi_netra'; // Nama database
$username = 'root'; // Username database
$password = ''; // Kosongkan jika tidak ada password

try {
    // Membuat koneksi menggunakan PDO
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Set mode error menjadi exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Jika terjadi error dalam koneksi
    die("Koneksi gagal: " . $e->getMessage());
}
?>
