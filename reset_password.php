<?php
session_start();
require 'db.php'; // Memasukkan file untuk koneksi database

// Pastikan pengguna sudah login
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit();
}

// Cek apakah form sudah dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $username = $_SESSION['username'];

    // Query database untuk memverifikasi password saat ini
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifikasi password
    if ($user && password_verify($currentPassword, $user['password'])) {
        // Hash password baru
        $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update password di database
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE username = ?");
        try {
            $stmt->execute([$hashedNewPassword, $username]);
            $success = "Password berhasil diubah.";
        } catch (PDOException $e) {
            $error = "Gagal mengubah password: " . $e->getMessage();
        }
    } else {
        $error = "Password saat ini salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Tambahkan styling yang sesuai */
    </style>
</head>
<body>
    <div class="reset-container">
        <h1>Reset Password</h1>
        <form method="POST" action="">
            <label for="current_password">Password Saat Ini:</label>
            <input type="password" name="current_password" required>
            <label for="new_password">Password Baru:</label>
            <input type="password" name="new_password" required>
            <button type="submit">Ubah Password</button>
        </form>

        <?php if (isset($success)): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
