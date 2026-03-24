<?php
require_once "inc/koneksi.php";

if (empty($_SESSION['username'])) {
    header("Location: form_login.php");
    exit;
}

$username = $_SESSION['username'];

/* ================= AMBIL EMAIL USER ================= */
$stmtUser = mysqli_prepare($koneksi, "SELECT email FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmtUser, "s", $username);
mysqli_stmt_execute($stmtUser);
$resultUser = mysqli_stmt_get_result($stmtUser);
$user = mysqli_fetch_assoc($resultUser);

if (!$user || empty($user['email'])) {
    echo "<p class='dashboard-empty'>Email tidak ditemukan.</p>";
    exit;
}

$emailUser = $user['email'];

/* ================= PAGINATION ================= */
$limit = 8; // 10 notifikasi per halaman
$page = isset($_GET['hal']) && is_numeric($_GET['hal']) ? (int)$_GET['hal'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;

/* ================= HITUNG TOTAL DATA ================= */
$stmtCount = mysqli_prepare($koneksi, "
    SELECT COUNT(*) as total
    FROM tbl_peminjaman
    WHERE LOWER(email_peminjam) = LOWER(?)
");
mysqli_stmt_bind_param($stmtCount, "s", $emailUser);
mysqli_stmt_execute($stmtCount);
$resultCount = mysqli_stmt_get_result($stmtCount);
$dataCount = mysqli_fetch_assoc($resultCount);

$totalData = $dataCount['total'];
$totalPage = ceil($totalData / $limit);

/* ================= QUERY RIWAYAT ================= */
$stmt = mysqli_prepare($koneksi, "
    SELECT p.*, b.judul_buku
    FROM tbl_peminjaman p
    LEFT JOIN tbl_buku b ON p.id_buku = b.id_buku
    WHERE LOWER(p.email_peminjam) = LOWER(?)
    ORDER BY p.tanggal_pinjam DESC
    LIMIT ?, ?
");

mysqli_stmt_bind_param($stmt, "sii", $emailUser, $offset, $limit);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Riwayat Peminjaman</title>

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
    box-shadow: 0 10px 30px rgba(107,62,38,0.3);
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
    box-shadow: 0 6px 20px rgba(0,0,0,0.06);
    transition: 0.3s ease;
}

.dashboard-notif-box:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}

/* STATUS COLOR */
.dashboard-status-orange { color: #f57c00; font-weight:600; }
.dashboard-status-green { color: #2e7d32; font-weight:600; }
.dashboard-status-red { color: #c62828; font-weight:600; }
.dashboard-status-blue { color: #1565c0; font-weight:600; }

.dashboard-empty {
    margin-top: 10px;
    font-style: italic;
    opacity: 0.8;
}

.pagination {
            margin-top: 40px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .pagination a,
        .pagination a:visited,
        .pagination a:hover,
        .pagination a:active {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #8b6f47;
            border-radius: 50%;
            text-decoration: none;
            transition: 0.3s;
            color: white;
            /* tulisan putih */
        }

        .pagination a img {
            width: 18px;
            height: 18px;
        }

        .pagination a:hover {
            background: #6f5437;
        }

        .pagination a.active {
            background: #5c4033;
        }
</style>
</head>

<body class="dashboard-body">

<h2 class="dashboard-header">📖 Riwayat Peminjaman Buku 📖</h2>

<div class="dashboard-container">

<?php
if ($result && mysqli_num_rows($result) > 0) {

    while ($row = mysqli_fetch_assoc($result)) {

        $status = strtolower(trim($row['status']));
        $warnaClass = "";

        if ($status == 'menunggu') $warnaClass = "dashboard-status-orange";
        elseif ($status == 'dipinjamkan') $warnaClass = "dashboard-status-green";
        elseif ($status == 'terlambat') $warnaClass = "dashboard-status-red";
        elseif ($status == 'ditolak') $warnaClass = "dashboard-status-red";
        elseif ($status == 'dikembalikan') $warnaClass = "dashboard-status-blue";
        elseif ($status == 'menunggu_konfirmasi_kembali') $warnaClass = "dashboard-status-blue";
?>

    <div class="dashboard-notif-box">
        <b>Buku:</b> <?= htmlspecialchars($row['judul_buku'] ?? 'Buku tidak ditemukan'); ?><br><br>

        <b>Tanggal Pinjam:</b>
        <?= !empty($row['tanggal_pinjam']) ? date('d-m-Y', strtotime($row['tanggal_pinjam'])) : '-' ; ?><br>

        <b>Tanggal Kembali:</b>
        <?= !empty($row['tanggal_kembali']) ? date('d-m-Y', strtotime($row['tanggal_kembali'])) : '-' ; ?><br><br>

        <b>Status:</b>
        <span class="<?= $warnaClass; ?>">
            <?= ucfirst(str_replace('_',' ',$status)); ?>
        </span>
    </div>

<?php
    }

} else {
    echo "<p class='dashboard-empty'>Belum ada riwayat peminjaman.</p>";
}
?>

</div>
<?php if ($totalPage > 1): ?>
<div style="text-align:center; margin-bottom:50px;">

   <div class="pagination">

    <!-- PREV -->
    <?php if ($page > 1): ?>
        <a href="dashboard.php?page=riwayat_peminjaman&hal=<?= $page - 1; ?>" class="page-btn">
            <img src="cover/previous.png" alt="Previous">
        </a>
    <?php endif; ?>

    <!-- NUMBER -->
    <?php for ($i = 1; $i <= $totalPage; $i++): ?>
        <a href="dashboard.php?page=riwayat_peminjaman&hal=<?= $i; ?>"
           class="page-btn <?= ($i == $page) ? 'active' : '' ?>">
            <?= $i; ?>
        </a>
    <?php endfor; ?>

    <!-- NEXT -->
    <?php if ($page < $totalPage): ?>
        <a href="dashboard.php?page=riwayat_peminjaman&hal=<?= $page + 1; ?>" class="page-btn">
            <img src="cover/next.png" alt="Next">
        </a>
    <?php endif; ?>

</div>
<?php endif; ?>
</body>
</html>