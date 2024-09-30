<?php
session_start();
require 'db.php'; // Memasukkan file untuk koneksi database

// Cek apakah form sudah dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Query untuk menyimpan data user baru
    $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");

    try {
        $stmt->execute([$username, $hashedPassword]);
        $success = "User berhasil didaftarkan.";
    } catch (PDOException $e) {
        $error = "Gagal mendaftar: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f2f5;
            font-family: Arial, sans-serif;
        }

        .signup-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        .signup-container h1 {
            margin: 0 0 20px;
            font-size: 24px;
            color: #333;
        }

        .signup-container label {
            display: block;
            margin: 10px 0 5px;
            font-size: 16px;
            color: #555;
        }

        .signup-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .signup-container button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            margin-bottom: 10px; /* Spasi antara tombol signup dan tombol login */
        }

        .signup-container button:hover {
            background-color: #0056b3;
        }

        .signup-container p {
            margin: 10px 0;
        }

        .signup-container .success {
            color: green;
        }

        .signup-container .error {
            color: red;
        }

        @media (max-width: 600px) {
            .signup-container {
                padding: 15px;
            }

            .signup-container h1 {
                font-size: 20px;
            }

            .signup-container input {
                padding: 8px;
            }

            .signup-container button {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h1>Sign Up</h1>
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" name="username" required>
            <label for="password">Password:</label>
            <input type="password" name="password" required>
            <button type="submit">Sign Up</button>
        </form>

        <?php if (isset($success)): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <a href="login.php" class="back-to-login">Back to Login</a>
    </div>
</body>
</html>
