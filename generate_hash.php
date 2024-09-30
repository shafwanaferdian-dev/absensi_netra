<?php
// Setel koneksi ke database
require 'db.php'; // Sesuaikan jika perlu

// Daftar user dan password mereka
$users = [
    'adam' => 'adam123',
    'albar' => 'albar123',
    'dhika' => 'dhika123',
    'anggi' => 'anggi123',
    'rere' => 'rere123',
    'dera' => 'dera123',
    'devi' => 'devi123',
    'dio' => 'dio123',
    'eko' => 'eko123',
    'elfiana' => 'elfiana123',
    'fitria' => 'fitria123',
    'halim' => 'halim123',
    'hilda' => 'hilda123',
    'jami' => 'jami123',
    'soni' => 'soni123',
    'maratus' => 'maratus123',
    'millatulhaq' => 'millatulhaq123',
    'ahmad' => 'ahmad123',
    'hanif' => 'hanif123',
    'misbah' => 'misbah123',
    'rayhan' => 'rayhan123',
    'aldi' => 'aldi123',
    'rano' => 'rano123',
    'kahfie' => 'kahfie123',
    'rikky' => 'rikky123',
    'rizka' => 'rizka123',
    'amel' => 'amel123',
    'indran' => 'indran123',
    'afifa' => 'afifa123',
    'aroh' => 'aroh123',
    'godi' => 'godi123',
    'syifa' => 'syifa123',
    'tulus' => 'tulus123',
    'winda' => 'winda123',
    'yeni' => 'yeni123',
    'yuli' => 'yuli123',
    'yuni' => 'yuni123',
];

// Prepare SQL statement
$stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");

foreach ($users as $username => $password) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt->execute([$username, $hashedPassword]);
    echo "User $username berhasil ditambahkan.<br>";
}
?>
