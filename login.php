<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query untuk memeriksa kredensial
    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Sukses login
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $user['username']; // Menyimpan nama staf
        header('Location: index.php');
        exit();
    } else {
        // Gagal login
        $error = "Username atau password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            height: 100%;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #f8b400, #8e44ad);
        }

        .login-container {
            background-color: rgba(255, 255, 255, 0.2); /* Transparansi lebih lembut */
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        .login-container img {
            width: 100px;
            margin-bottom: 20px;
        }

        .login-container h1 {
            font-size: 24px;
            color: #fff;
            margin-bottom: 30px;
        }

        .login-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: none;
            border-radius: 30px;
            box-sizing: border-box;
            background-color: rgba(255, 255, 255, 0.3);
            color: #fff;
            font-size: 16px;
        }

        .login-container input::placeholder {
            color: #ddd;
        }

        .login-container button,
        .login-container .signup-button {
            width: 100%;
            padding: 10px;
            background-color: #3498db; /* Warna biru untuk tombol login dan sign up */
            border: none;
            border-radius: 30px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }

        .login-container button:hover,
        .login-container .signup-button:hover {
            background-color: #2980b9; /* Warna biru gelap saat hover */
        }

        .login-container .forgot-password {
            display: block;
            margin-top: 10px;
            font-size: 14px;
            color: #fff;
            text-decoration: none;
        }

        .login-container .forgot-password:hover {
            text-decoration: underline;
        }

        .login-container .error {
            color: red;
            margin-top: 10px;
        }

        @media (max-width: 600px) {
            .login-container {
                padding: 20px;
            }

            .login-container input, .login-container button, .login-container .signup-button {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="https://cdn-icons-png.flaticon.com/512/9187/9187604.png" alt="User Icon"> <!-- Ganti dengan ikon pengguna sesuai gambar -->
        <h1>ABSENSI NETRA KLINIK</h1>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <form id="signup-form">
            <button type="button" onclick="redirectToSignUp()">Sign Up</button>
        </form>
    </div>

    <script>
        function redirectToSignUp() {
            window.location.href = 'sign_up.php';
        }
    </script>
</body>
</html>