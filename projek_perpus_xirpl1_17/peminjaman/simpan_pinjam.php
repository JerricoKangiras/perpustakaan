<?php
require_once __DIR__ . '/../inc/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$username = $_SESSION['username'] ?? '';

if ($username === '') {
    die("User belum login.");
}

/* ===============================
   AMBIL DATA USER
=================================*/
$stmtUser = mysqli_prepare($koneksi,
    "SELECT username, email, no_tlp FROM users WHERE username = ?"
);
mysqli_stmt_bind_param($stmtUser, "s", $username);
mysqli_stmt_execute($stmtUser);
$resultUser = mysqli_stmt_get_result($stmtUser);
$user = mysqli_fetch_assoc($resultUser);

if (!$user) {
    die("User tidak ditemukan.");
}

/* ===============================
   CEK LIMIT PINJAM
=================================*/
$stmtCek = mysqli_prepare($koneksi,
    "SELECT COUNT(*) AS total
     FROM tbl_peminjaman
     WHERE nama_peminjam = ?
     AND status IN ('dipinjamkan','Menunggu_Konfirmasi_Kembali')"
);
mysqli_stmt_bind_param($stmtCek, "s", $username);
mysqli_stmt_execute($stmtCek);
$resultCek = mysqli_stmt_get_result($stmtCek);
$dataCek = mysqli_fetch_assoc($resultCek);

if ($dataCek['total'] >= 5) {
    $error = "Limit peminjaman sudah mencapai 5 buku.";
}

/* ===============================
   PROSES INSERT
=================================*/
if (!isset($error)) {

$id_buku = $_POST['id_buku'];
$tanggal_pinjam = $_POST['tanggal_pinjam'];
$durasi_pinjam = (int)$_POST['durasi_pinjam'];
$tanggal_kembali = date('Y-m-d', strtotime($tanggal_pinjam . " +$durasi_pinjam days"));
$status = "menunggu";

$stmtInsert = mysqli_prepare($koneksi, "INSERT INTO tbl_peminjaman
(id_buku, nama_peminjam, email_peminjam, notlp_peminjam, tanggal_pinjam, tanggal_kembali, durasi_pinjam, status)
VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

mysqli_stmt_bind_param(
    $stmtInsert,
    "isssssis",
    $id_buku,
    $user['username'],
    $user['email'],
    $user['no_tlp'],
    $tanggal_pinjam,
    $tanggal_kembali,
    $durasi_pinjam,
    $status
);

$eksekusi = mysqli_stmt_execute($stmtInsert);

if (!$eksekusi) {
    $error = mysqli_error($koneksi);
}

}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Status Peminjaman</title>

<style>
body{
    font-family:'Poppins',sans-serif;
    display:flex;
    align-items:center;
    justify-content:center;
    height:100vh;
    margin:0;
}

/* CARD */
.result-box{
    background:#fef2f2;
    padding:45px 55px;
    border-radius:25px;
    border:1px solid #fecaca;
    box-shadow:0 15px 40px rgba(185,28,28,0.15);
    text-align:center;
    width:420px;
    animation:fadeIn 0.4s ease-in-out;
}

/* SUCCESS */
.success-title{
    color:#7a1f1f;
    margin-bottom:15px;
}

/* ERROR */
.error-title{
    color:#b91c1c;
    margin-bottom:15px;
}

p{
    font-size:15px;
    color:#5a1f1f;
    margin-bottom:30px;
}

/* BUTTON */
.btn{
    display:inline-block;
    background:linear-gradient(to right,#7a1f1f,#b91c1c);
    color:#fff;
    padding:12px 28px;
    border-radius:40px;
    text-decoration:none;
    font-weight:600;
    transition:0.3s;
}

.btn:hover{
    transform:translateY(-2px);
    box-shadow:0 6px 15px rgba(185,28,28,0.3);
}

.btn-error{
    background:#b91c1c;
}

@keyframes fadeIn{
    from{opacity:0;transform:translateY(-10px);}
    to{opacity:1;transform:translateY(0);}
}
</style>
</head>

<body>
<div class="result-box">

<?php if (isset($error)) { ?>

    <h2 class="error-title">⚠️ Terjadi Kesalahan!</h2>
    <p><?= htmlspecialchars($error) ?></p>
    <a href="dashboard.php?page=pinjam" class="btn btn-error">Kembali</a>

<?php } else { ?>

    <h2 class="success-title">📕 Pengajuan Berhasil!</h2>
    <p>Pengajuan peminjaman sedang menunggu konfirmasi admin.</p>
    <a href="dashboard.php?page=pinjam" class="btn">Kembali</a>

<?php } ?>

</div>
</body>
</html>