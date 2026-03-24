<?php
require_once "inc/koneksi.php";

if (empty($_SESSION['username'])) {
    header("Location: form_login.php");
    exit;
}

$username = $_SESSION['username'];

/* ================= AMBIL DATA USER ================= */
$stmtUser = mysqli_prepare($koneksi, "SELECT email FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmtUser, "s", $username);
mysqli_stmt_execute($stmtUser);
$resultUser = mysqli_stmt_get_result($stmtUser);
$user = mysqli_fetch_assoc($resultUser);

if (!$user) {
    session_destroy();
    header("Location: form_login.php");
    exit;
}

$emailUser = $user['email'];

if (empty($emailUser)) {
    echo "<p>Email tidak ditemukan.</p>";
    exit;
}

/* ================= PROSES AJUKAN PENGEMBALIAN ================= */
if (isset($_GET['id'])) {

    $id = intval($_GET['id']);

    $stmtUpdate = mysqli_prepare($koneksi, "
        UPDATE tbl_peminjaman
        SET status = 'Menunggu_Konfirmasi_Kembali'
        WHERE id_peminjam = ?
        AND LOWER(email_peminjam) = LOWER(?)
        AND status IN ('Dipinjamkan','Terlambat')
    ");

    mysqli_stmt_bind_param($stmtUpdate, "is", $id, $emailUser);
    mysqli_stmt_execute($stmtUpdate);

    if (mysqli_stmt_affected_rows($stmtUpdate) > 0) {
        echo "<script>
            alert('Pengajuan pengembalian berhasil dikirim!');
            window.location='dashboard.php?page=proses_kembali';
        </script>";
        exit;
    } else {
        echo "<script>
            alert('Gagal mengajukan pengembalian atau status tidak valid.');
            window.location='dashboard.php?page=proses_kembali';
        </script>";
        exit;
    }
}

/* ================= TAMPILKAN RIWAYAT ================= */
$stmt = mysqli_prepare($koneksi, "
    SELECT p.*, b.judul_buku
    FROM tbl_peminjaman p
    LEFT JOIN tbl_buku b ON p.id_buku = b.id_buku
    WHERE LOWER(p.email_peminjam) = LOWER(?)
    ORDER BY p.tanggal_pinjam DESC
");

mysqli_stmt_bind_param($stmt, "s", $emailUser);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {

    while ($row = mysqli_fetch_assoc($result)) {

        $status = strtolower(trim($row['status']));
        $warna = "black";

        if ($status == 'menunggu') $warna = "orange";
        elseif ($status == 'dipinjamkan') $warna = "green";
        elseif ($status == 'terlambat') $warna = "red";
        elseif ($status == 'ditolak') $warna = "gray";
        elseif ($status == 'dikembalikan') $warna = "#0d6efd";
        elseif ($status == 'menunggu_konfirmasi_kembali') $warna = "blue";
?>

<?php
    }

} else {
    echo "<p>Belum ada riwayat peminjaman.</p>";
}
?>