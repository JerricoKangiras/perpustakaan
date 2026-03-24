<?php
session_start();
include "inc/koneksi.php";

if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit;
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $hasil = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($hasil) > 0) {
        $_SESSION['username'] = $username;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Maaf, login gagal!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Perpustakaan Digital</title>
</head>
<style>
    /* ================== GLOBAL ================== */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', sans-serif;
}

body {
    background-color: #fffaf2;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    color: #4a2c1a;
}

/* ================== HEADER ================== */
.login-header {
    background: linear-gradient(135deg, #6b3e26, #8b5e3c);
    padding: 20px 40px;
    color: white;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
}

.login-header h1 {
    font-size: 22px;
    letter-spacing: 1px;
}

/* ================== MAIN ================== */
.login-main {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 40px 20px;
}

/* ================== CARD ================== */
.login-card {
    background: white;
    width: 100%;
    max-width: 420px;
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(107, 62, 38, 0.2);
}

.login-title {
    text-align: center;
    margin-bottom: 10px;
    font-size: 28px;
    font-weight: bold;
}

.login-subtitle {
    text-align: center;
    font-size: 14px;
    margin-bottom: 30px;
    color: #8b5e3c;
}

/* ================== FORM ================== */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border-radius: 10px;
    border: 1px solid #d6c2b5;
    background-color: #fffaf2;
    transition: 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: #8b5e3c;
    box-shadow: 0 0 0 3px rgba(139, 94, 60, 0.2);
}

/* ================== BUTTON ================== */
.login-button {
    width: 100%;
    padding: 12px;
    border-radius: 30px;
    border: none;
    font-weight: bold;
    cursor: pointer;
    background: linear-gradient(135deg, #6b3e26, #8b5e3c);
    color: white;
    transition: 0.3s;
}

.login-button:hover {
    opacity: 0.9;
    transform: translateY(-2px);
}

/* ================== ERROR ================== */
.login-error {
    background-color: #ffe5e0;
    border: 1px solid #ffb3a7;
    color: #b23b2a;
    padding: 10px;
    border-radius: 10px;
    text-align: center;
    margin-bottom: 20px;
}

/* ================== FOOTER ================== */
.login-footer {
    background: linear-gradient(135deg, #6b3e26, #8b5e3c);
    text-align: center;
    padding: 20px;
    color: white;
    font-size: 14px;
}
   </style>
<body>

    <!-- ================= HEADER ================= -->
    <header class="login-header">
        <h1>📚 Perpustakaan Digital</h1>
    </header>

    <!-- ================= MAIN ================= -->
    <main class="login-main">

        <div class="login-card">

            <h2 class="login-title">Login Akun</h2>
            <p class="login-subtitle">
                Masuk untuk mengakses website perpustakaan
            </p>

            <?php if (isset($error)) { ?>
                <div class="login-error">
                    <?= $error ?>
                </div>
            <?php } ?>

            <form method="post">

                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required class="form-control">
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required class="form-control">
                </div>

                <button type="submit" name="login" class="login-button">
                    Login
                </button>

            </form>

        </div>

    </main>

    <!-- ================= FOOTER ================= -->
    <footer class="login-footer">
        <p>© <?= date("Y") ?> Perpustakaan Digital. All rights reserved.</p>
    </footer>

</body>
</html>