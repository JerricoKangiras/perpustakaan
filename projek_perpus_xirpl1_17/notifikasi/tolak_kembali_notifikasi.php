<?php
require_once "inc/koneksi.php";

if (empty($_SESSION['username'])) {
    header("Location: form_login.php");
    exit;
}

// cek role admin
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

// proses penolakan
if (isset($_GET['id_peminjam'])) {

    $idp = (int) $_GET['id_peminjam'];

    // ambil data peminjaman
    $stmtAmbil = mysqli_prepare($koneksi,
        "SELECT status FROM tbl_peminjaman WHERE id_peminjam = ?"
    );
    mysqli_stmt_bind_param($stmtAmbil, "i", $idp);
    mysqli_stmt_execute($stmtAmbil);
    $resultAmbil = mysqli_stmt_get_result($stmtAmbil);
    $data = mysqli_fetch_assoc($resultAmbil);

    if ($data && strtolower($data['status']) == 'menunggu') {

        // update status jadi ditolak
        $stmtUpdate = mysqli_prepare($koneksi,
            "UPDATE tbl_peminjaman 
             SET status = 'ditolak' 
             WHERE id_peminjam = ?"
        );
        mysqli_stmt_bind_param($stmtUpdate, "i", $idp);
        mysqli_stmt_execute($stmtUpdate);

        echo "<script>
        alert('Peminjaman berhasil ditolak!');
        window.location='dashboard.php?page=notifikasi';
        </script>";

    } else {
        echo "<script>
        alert('Data tidak valid atau sudah diproses.');
        window.location='dashboard.php?page=notifikasi';
        </script>";
    }

    exit;

} else {
    header("Location: dashboard.php?page=notifikasi");
    exit;
}
?>