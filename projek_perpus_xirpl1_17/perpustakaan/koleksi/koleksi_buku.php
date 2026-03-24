<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "inc/koneksi.php";

if (empty($_SESSION['username'])) {
    header("Location: form_login.php");
    exit;
}

$username = $_SESSION['username'];
$pesanSukses = "";

/* ================= AMBIL EMAIL USER ================= */
$stmtUser = mysqli_prepare($koneksi, "SELECT email FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmtUser, "s", $username);
mysqli_stmt_execute($stmtUser);
$resultUser = mysqli_stmt_get_result($stmtUser);
$user = mysqli_fetch_assoc($resultUser);

if (!$user || empty($user['email'])) {
    die("Email tidak ditemukan.");
}

$emailUser = $user['email'];

/* ================= PROSES AJUKAN PENGEMBALIAN ================= */
if (isset($_GET['kembali'])) {

    $id = intval($_GET['kembali']);

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
        $pesanSukses = "Pengajuan pengembalian berhasil dikirim.";
    }
}

/* ================= QUERY BUKU YANG DIPINJAM ================= */
$stmt = mysqli_prepare($koneksi, "
    SELECT p.*, b.judul_buku
    FROM tbl_peminjaman p
    LEFT JOIN tbl_buku b ON p.id_buku = b.id_buku
    WHERE LOWER(p.email_peminjam) = LOWER(?)
    AND p.status IN ('Dipinjamkan','Terlambat','Menunggu_Konfirmasi_Kembali')
    ORDER BY p.tanggal_pinjam DESC
");

mysqli_stmt_bind_param($stmt, "s", $emailUser);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
html, body {
    background: #f6f1e8;
    margin: 0;
    padding: 0;
}

.dashboard-body {
    font-family: 'Poppins', sans-serif;
    background: #f6f1e8;
}

/* HEADER */
.dashboard-header {
    max-width: 1100px;
    margin: 40px auto 0;
    padding: 20px 40px;
    background: linear-gradient(135deg, #6b3e26, #8b5e3c);
    border-radius: 50px;
    box-shadow: 0 10px 30px rgba(107, 62, 38, 0.3);
    text-align: center;
    color: #fff8f0;
    font-size: 20px;
    font-weight: 600;
}

/* CONTAINER */
.dashboard-container {
    max-width: 1100px;
    margin: 30px auto 60px;
    padding: 0 20px;
}

/* CARD */
.dashboard-notif-box {
    border: 1px solid #e5d3c5;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 15px;
    background: #f6f1e8;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
    transition: 0.3s ease;
}

.dashboard-notif-box:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
}

/* BUTTON */
.dashboard-btn {
    padding: 8px 16px;
    color: white;
    text-decoration: none;
    border-radius: 30px;
    font-size: 14px;
    display: inline-block;
    margin-top: 12px;
    transition: 0.3s ease;
}

.dashboard-btn-blue { background: #1565c0; }

.dashboard-btn:hover {
    opacity: 0.85;
    transform: translateY(-2px);
}

/* STATUS */
.dashboard-status-green { color: #2e7d32; font-weight: 600; }
.dashboard-status-red { color: #c62828; font-weight: 600; }
.dashboard-status-blue { color: #1565c0; font-weight: 600; }

.dashboard-empty {
    margin-top: 10px;
    font-style: italic;
    opacity: 0.8;
}

/* SUCCESS MESSAGE */
.dashboard-success {
    background: #e8f5e9;
    border: 1px solid #c8e6c9;
    padding: 12px 20px;
    border-radius: 30px;
    margin-bottom: 20px;
    color: #2e7d32;
    text-align: center;
}
</style>
</head>

<body class="dashboard-body">

<h2 class="dashboard-header">📚 Koleksi Buku Saya 📚</h2>

<div class="dashboard-container">

<?php if (!empty($pesanSukses)) { ?>
    <div class="dashboard-success">
        ✅ <?= htmlspecialchars($pesanSukses); ?>
    </div>
<?php } ?>

<?php
if ($result && mysqli_num_rows($result) > 0) {

    while ($row = mysqli_fetch_assoc($result)) {

        $status = strtolower($row['status']);
        $warnaClass = "";

        if ($status == 'dipinjamkan')
            $warnaClass = "dashboard-status-green";
        elseif ($status == 'terlambat')
            $warnaClass = "dashboard-status-red";
        elseif ($status == 'menunggu_konfirmasi_kembali')
            $warnaClass = "dashboard-status-blue";
?>

<div class="dashboard-notif-box">

    <b>Buku:</b> <?= htmlspecialchars($row['judul_buku'] ?? 'Buku tidak ditemukan'); ?><br>
    <b>Tanggal Pinjam:</b> <?= date('d-m-Y', strtotime($row['tanggal_pinjam'])); ?><br>

    <?php if (!empty($row['tanggal_kembali'])) { ?>
        <b>Batas Kembali:</b> <?= date('d-m-Y', strtotime($row['tanggal_kembali'])); ?><br>
    <?php } ?>

    <b>Status:</b>
    <span class="<?= $warnaClass; ?>">
        <?= ucfirst(str_replace('_',' ',$status)); ?>
    </span>

    <?php if ($status == 'dipinjamkan' || $status == 'terlambat') { ?>
        <br>
        <a href="dashboard.php?page=koleksi_buku&kembali=<?= $row['id_peminjam']; ?>"
           onclick="return confirm('Yakin ingin mengajukan pengembalian buku ini?')"
           class="dashboard-btn dashboard-btn-blue">
           🔄 Ajukan Pengembalian
        </a>
    <?php } ?>

</div>

<?php
    }

} else {
    echo "<p class='dashboard-empty'>Tidak ada buku yang sedang dipinjam.</p>";
}
?>

</div>
</body>
</html>