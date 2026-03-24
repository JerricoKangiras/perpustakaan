<?php
require_once "inc/koneksi.php";

if (empty($_SESSION['username'])) {
    header("Location: form_login.php");
    exit;
}

$username = $_SESSION['username'];

/* ================= AMBIL DATA USER ================= */
$stmtUser = mysqli_prepare($koneksi, "SELECT akses, email FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmtUser, "s", $username);
mysqli_stmt_execute($stmtUser);
$resultUser = mysqli_stmt_get_result($stmtUser);
$user = mysqli_fetch_assoc($resultUser);

if (!$user) {
    session_destroy();
    header("Location: form_login.php");
    exit;
}

$role = $user['akses'];
$emailUser = $user['email'] ?? null;

/* ================= AUTO HAPUS NOTIF DITOLAK > 24 JAM ================= */
$stmtDelete = mysqli_prepare($koneksi, "
    DELETE FROM tbl_peminjaman 
    WHERE status = 'ditolak' 
    AND tanggal_pinjam < DATE_SUB(NOW(), INTERVAL 1 DAY)
");
mysqli_stmt_execute($stmtDelete);

/* ================= PAGINATION ================= */
$limit = 8;
$page = isset($_GET['hal']) && is_numeric($_GET['hal']) ? (int) $_GET['hal'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;

$totalData = 0;
$totalPage = 1;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>

    <style>
        html,
        body {
            background: #f6f1e8;
            margin: 0;
            padding: 0;
        }

        .main {
            background: #f6f1e8;
            margin-top: 40px;
        }

        html {
            background: #f6f1e8;
        }

        .dashboard-body {
            font-family: 'Poppins', sans-serif;
            background: #f6f1e8;
            padding: 0;
            margin: 0;
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

        /* CONTENT WRAPPER */
        .dashboard-container {
            max-width: 1100px;
            margin: 30px auto 60px;
            padding: 0 20px;
        }

        /* NOTIFICATION CARD */
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
            margin-right: 8px;
            transition: 0.3s ease;
        }

        .dashboard-btn-green {
            background: #2e7d32;
        }

        .dashboard-btn-red {
            background: #c62828;
        }

        .dashboard-btn-blue {
            background: #1565c0;
        }

        .dashboard-btn:hover {
            opacity: 0.85;
            transform: translateY(-2px);
        }

        /* STATUS */
        .dashboard-status-orange {
            color: #f57c00;
            font-weight: 600;
        }

        .dashboard-status-green {
            color: #2e7d32;
            font-weight: 600;
        }

        .dashboard-status-red {
            color: #c62828;
            font-weight: 600;
        }

        .dashboard-status-blue {
            color: #1565c0;
            font-weight: 600;
        }

        .dashboard-empty {
            margin-top: 10px;
            font-style: italic;
            opacity: 0.8;
        }

        .pagination {
            margin-top: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
        }

        .pagination a {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #8b6f47;
            border-radius: 50%;
            text-decoration: none;
            transition: all 0.3s ease;
            color: white;
            font-weight: 500;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .pagination a img {
            width: 18px;
            height: 18px;
        }

        .pagination a:hover {
            background: #6f5437;
            transform: translateY(-3px);
        }

        .pagination a.active {
            background: #5c4033;
            font-weight: 600;
            transform: scale(1.05);
        }
    </style>
</head>

<body class="dashboard-body">

    <h2 class="dashboard-header">🔔 Notifikasi Perpustakaan 🔔</h2>

    <div class="dashboard-container">
        <?php
        /* ================= ADMIN ================= */
        if ($role === 'admin') {

            /* HITUNG TOTAL DATA ADMIN */
            $stmtCount = mysqli_prepare($koneksi, "
        SELECT COUNT(*) as total
        FROM tbl_peminjaman
        WHERE status IN ('Menunggu','Menunggu_Konfirmasi_Kembali')
    ");
            mysqli_stmt_execute($stmtCount);
            $resultCount = mysqli_stmt_get_result($stmtCount);
            $dataCount = mysqli_fetch_assoc($resultCount);
            $totalData = $dataCount['total'];

            $totalPage = ceil($totalData / $limit);

            /* AMBIL DATA SESUAI HALAMAN */
            $stmt = mysqli_prepare($koneksi, "
    SELECT p.*, b.judul_buku 
    FROM tbl_peminjaman p
    LEFT JOIN tbl_buku b ON p.id_buku = b.id_buku
    WHERE LOWER(p.status) IN ('menunggu','menunggu_konfirmasi_kembali')
    ORDER BY 
        CASE 
            WHEN LOWER(p.status) = 'menunggu' THEN 1
            WHEN LOWER(p.status) = 'menunggu_konfirmasi_kembali' THEN 2
            ELSE 3
        END,
        p.tanggal_pinjam DESC
    LIMIT ? OFFSET ?
");
            mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {

                while ($row = mysqli_fetch_assoc($result)) {

                    $status = strtolower($row['status']);
                    ?>

                    <div class="dashboard-notif-box">
                        <b><?= htmlspecialchars($row['nama_peminjam']); ?></b><br>
                        Buku: <?= htmlspecialchars($row['judul_buku'] ?? 'Buku tidak ditemukan'); ?><br>
                        Tanggal Pinjam: <?= date('d-m-Y', strtotime($row['tanggal_pinjam'])); ?><br><br>

                        <?php if ($status == 'menunggu') { ?>

                            <div class="dashboard-status-orange">Menunggu Konfirmasi Peminjaman</div><br>

                            <a href="dashboard.php?page=konfirmasi_pinjam&id=<?= $row['id_peminjam']; ?>"
                                onclick="return confirm('Konfirmasi peminjaman ini?')" class="dashboard-btn dashboard-btn-green">
                                ✅ Konfirmasi
                            </a>

                            <a href="dashboard.php?page=tolak_pinjam&id=<?= $row['id_peminjam']; ?>"
                                onclick="return confirm('Tolak peminjaman ini?')" class="dashboard-btn dashboard-btn-red">
                                ❌ Tolak
                            </a>

                        <?php } elseif ($status == 'menunggu_konfirmasi_kembali') { ?>

                            <div class="dashboard-status-blue">Menunggu Konfirmasi Pengembalian</div><br>

                            <a href="dashboard.php?page=konfirmasi_kembali&id=<?= $row['id_peminjam']; ?>"
                                onclick="return confirm('Konfirmasi pengembalian buku ini?')" class="dashboard-btn dashboard-btn-blue">
                                🔄 Konfirmasi Pengembalian
                            </a>

                        <?php } ?>

                    </div>

                    <?php
                }

            } else {
                echo "<p class='dashboard-empty'>Tidak ada notifikasi.</p>";
            }
        }

        /* ================= ANGGOTA ================= */ elseif ($role === 'anggota') {

            if (empty($emailUser)) {
                echo "<p class='dashboard-empty'>Email tidak ditemukan.</p>";
                exit;
            }

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

            if ($page > $totalPage && $totalPage > 0) {
                $page = $totalPage;
                $offset = ($page - 1) * $limit;
            }

            /* ================= AMBIL DATA SESUAI HALAMAN ================= */
            $stmt = mysqli_prepare($koneksi, "
        SELECT p.*, b.judul_buku
        FROM tbl_peminjaman p
        LEFT JOIN tbl_buku b ON p.id_buku = b.id_buku
        WHERE LOWER(p.email_peminjam) = LOWER(?)
        ORDER BY p.tanggal_pinjam DESC
        LIMIT ? OFFSET ?
    ");
            mysqli_stmt_bind_param($stmt, "sii", $emailUser, $limit, $offset);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {

                while ($row = mysqli_fetch_assoc($result)) {

                    $status = strtolower($row['status']);
                    $warnaClass = "";

                    if ($status == 'menunggu')
                        $warnaClass = "dashboard-status-orange";
                    elseif ($status == 'dipinjamkan')
                        $warnaClass = "dashboard-status-green";
                    elseif ($status == 'terlambat')
                        $warnaClass = "dashboard-status-red";
                    elseif ($status == 'ditolak')
                        $warnaClass = "dashboard-status-red";
                    ?>

                    <div class="dashboard-notif-box">
                        Buku: <?= htmlspecialchars($row['judul_buku'] ?? 'Buku tidak ditemukan'); ?><br>
                        Tanggal Pinjam: <?= date('d-m-Y', strtotime($row['tanggal_pinjam'])); ?><br>
                        Tanggal Kembali: <?= date('d-m-Y', strtotime($row['tanggal_kembali'])); ?><br>
                        Status: <span class="<?= $warnaClass; ?>"><?= ucfirst($status); ?></span>

                        <?php if ($status == 'ditolak') { ?>
                            <div class="dashboard-status-red" style="margin-top:6px;">
                                Maaf, pengajuan buku Anda ditolak.
                                Notifikasi ini akan hilang otomatis dalam 24 jam.
                            </div>
                        <?php } ?>

                    </div>

                    <?php
                }

            } else {
                echo "<p class='dashboard-empty'>Belum ada notifikasi.</p>";
            }
        }
        ?>
        <?php if ($totalPage > 1): ?>
            <div class="pagination">

                <!-- PREV -->
                <?php if ($page > 1): ?>
                    <a href="dashboard.php?page=notifikasi&hal=<?= $page - 1; ?>">
                        <img src="cover/previous.png" alt="Previous">
                    </a>
                <?php endif; ?>

                <!-- NUMBER -->
                <?php for ($i = 1; $i <= $totalPage; $i++): ?>
                    <a href="dashboard.php?page=notifikasi&hal=<?= $i; ?>" class="<?= ($i == $page) ? 'active' : '' ?>">
                        <?= $i; ?>
                    </a>
                <?php endfor; ?>

                <!-- NEXT -->
                <?php if ($page < $totalPage): ?>
                    <a href="dashboard.php?page=notifikasi&hal=<?= $page + 1; ?>">
                        <img src="cover/next.png" alt="Next">
                    </a>
                <?php endif; ?>

            </div>
        <?php endif; ?>
</body>

</html>