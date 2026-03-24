<?php
require_once "inc/koneksi.php";

if (empty($_SESSION['username'])) {
    header("Location: form_login.php");
    exit;
}

$username = $_SESSION['username'];
$stmtUser = mysqli_prepare($koneksi, "SELECT akses FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmtUser, "s", $username);
mysqli_stmt_execute($stmtUser);
$resultUser = mysqli_stmt_get_result($stmtUser);
$user = mysqli_fetch_assoc($resultUser);

if ($user['akses'] != 'admin') {
    echo "<script>
    alert('Akses ditolak!');
    window.location='dashboard.php';
    </script>";
    exit;
}

$status = "";
$pesan = "";
$link = "dashboard.php?page=notifikasi";

if (isset($_GET['id'])) {
    $idp = (int) $_GET['id'];

    $stmtAmbil = mysqli_prepare($koneksi,
        "SELECT id_buku, status FROM tbl_peminjaman WHERE id_peminjam = ?"
    );
    mysqli_stmt_bind_param($stmtAmbil, "i", $idp);
    mysqli_stmt_execute($stmtAmbil);
    $resultAmbil = mysqli_stmt_get_result($stmtAmbil);
    $data = mysqli_fetch_assoc($resultAmbil);

    if ($data && strtolower($data['status']) == 'menunggu') {

        $id_buku = $data['id_buku'];

        $stmtStok = mysqli_prepare($koneksi,
            "SELECT jumlah_buku FROM tbl_buku WHERE id_buku = ?"
        );
        mysqli_stmt_bind_param($stmtStok, "i", $id_buku);
        mysqli_stmt_execute($stmtStok);
        $resultStok = mysqli_stmt_get_result($stmtStok);
        $stokData = mysqli_fetch_assoc($resultStok);

        if ($stokData['jumlah_buku'] > 0) {

            $stmtUpdateStok = mysqli_prepare($koneksi,
                "UPDATE tbl_buku 
                 SET jumlah_buku = jumlah_buku - 1 
                 WHERE id_buku = ? AND jumlah_buku > 0"
            );
            mysqli_stmt_bind_param($stmtUpdateStok, "i", $id_buku);
            mysqli_stmt_execute($stmtUpdateStok);

            $stmtUpdate = mysqli_prepare($koneksi,
                "UPDATE tbl_peminjaman 
                 SET status = 'dipinjamkan' 
                 WHERE id_peminjam = ?"
            );
            mysqli_stmt_bind_param($stmtUpdate, "i", $idp);
            mysqli_stmt_execute($stmtUpdate);

            $status = "success";
            $pesan = "Peminjaman berhasil dikonfirmasi!";

        } else {
            $status = "error";
            $pesan = "Stok buku habis! Tidak bisa dikonfirmasi.";
        }

    } else {
        $status = "error";
        $pesan = "Data tidak valid atau sudah dikonfirmasi.";
    }

} else {
    header("Location: dashboard.php?page=notifikasi");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Status Konfirmasi</title>
<style>
body {
    font-family: 'Poppins', sans-serif;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
    margin: 0;
}

.result-box {
    background: #f5e6d3;
    padding: 45px 55px;
    border-radius: 25px;
    border: 1px solid #e0c3a3;
    box-shadow: 0 15px 40px rgba(139,94,60,0.15);
    text-align: center;
    width: 420px;
    animation: fadeIn 0.4s ease-in-out;
}

.success-title {
    color: #6b3e26;
    margin-bottom: 15px;
}

.error-title {
    color: #a94442;
    margin-bottom: 15px;
}

p {
    font-size: 15px;
    color: #5a3825;
    margin-bottom: 30px;
}

.btn {
    display: inline-block;
    background: linear-gradient(to right, #6b3e26, #8b5e3c);
    color: #fff;
    padding: 12px 28px;
    border-radius: 40px;
    text-decoration: none;
    font-weight: 600;
    transition: 0.3s;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(107,62,38,0.3);
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
</head>

<body>
<div class="result-box">
    <?php if ($status == "success") { ?>
        <h2 class="success-title">✅ Berhasil!</h2>
        <p><?= $pesan ?></p>
    <?php } else { ?>
        <h2 class="error-title">⚠️ Gagal!</h2>
        <p><?= $pesan ?></p>
    <?php } ?>
    <a href="<?= $link ?>" class="btn">Kembali ke Notifikasi</a>
</div>
</body>
</html>