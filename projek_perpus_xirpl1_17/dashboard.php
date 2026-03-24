<?php
session_start();
require_once "inc/koneksi.php";

/* ===== CEK LOGIN ===== */
if (empty($_SESSION['username'])) {
    header("Location: form_login.php");
    exit;
}

$username = $_SESSION['username'];

/* ===== AMBIL DATA USER + AKSES ===== */
$stmt = mysqli_prepare($koneksi, "SELECT id, username, akses FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    session_destroy();
    header("Location: form_login.php");
    exit;
}

$role = $user['akses']; // ambil dari kolom akses

/* ===== ROUTING PAGE ===== */
$page = $_GET['page'] ?? 'awal';

/* ===== DAFTAR PAGE YANG BOLEH DIAKSES ===== */
$allowed_pages = [
    'awal',
    'riwayat_peminjaman',
    'notifikasi',
    'konfirmasi_pinjam',
    'tolak_pinjam',
    'konfirmasi_kembali',
    'tolak_kembali',
    'perpustakaan',
    'koleksi_buku',
    'proses_kembali',
    'detail_buku_invincible',
    'detail_buku_jjk',
    'detail_buku_csm',
    'detail_buku_detective_conan',
    'detail_buku_tawog',
    'profile',
    'buku',
    'tambah_buku',
    'simpan_buku',
    'edit_buku',
    'simpan_edit_buku',
    'hapus_buku',
    'kategori',
    'tambah_kategori',
    'simpan_kategori',
    'edit_kategori',
    'simpan_edit_kategori',
    'hapus_kategori',
    'penerbit',
    'tambah_penerbit',
    'simpan_penerbit',
    'edit_penerbit',
    'simpan_edit_penerbit',
    'hapus_penerbit',
    'peminjaman',
    'pinjam',
    'simpan_pinjam'

];

if (!in_array($page, $allowed_pages)) {
    $page = '404';
}

/* ===== BATASI AKSES BERDASARKAN ROLE ===== */
if ($role == 'anggota') {

    $admin_only_pages = [
        'buku',
        'tambah_buku',
        'simpan_buku',
        'edit_buku',
        'simpan_edit_buku',
        'hapus_buku',
        'kategori',
        'tambah_kategori',
        'simpan_kategori',
        'edit_kategori',
        'simpan_edit_kategori',
        'hapus_kategori',
        'penerbit',
        'tambah_penerbit',
        'simpan_penerbit',
        'edit_penerbit',
        'simpan_edit_penerbit',
        'hapus_penerbit',
        'peminjaman',
        'konfirmasi_pinjam',
        'tolak_pinjam',
        'konfirmasi_kembali',
        'tolak_kembali'
    ];

    if (in_array($page, $admin_only_pages)) {
        include "403.php";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Perpustakaan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="tailwind.css">
    <link rel="stylesheet" href="dashboard.css">
</head>

<body>

    <!-- ===== HEADER ===== -->
    <div class="header">
        <span>
            Selamat Datang,
            <b><?= htmlspecialchars($user['username']); ?></b>
            (<?= ucfirst($role); ?>) 👋
        </span>
    </div>

    <!-- ===== SIDEBAR ===== -->
    <div class="sidebar">
        <ul>

            <!-- ===== UTAMA ===== -->
            <li class="menu-title">📊 UTAMA</li>
            <li>
                <a href="dashboard.php?page=awal" class="<?= $page == 'awal' ? 'active' : '' ?>">
                    Dashboard
                </a>
            </li>

            <!-- ===== MANAJEMEN DATA (ADMIN ONLY) ===== -->
            <?php if ($role == 'admin'): ?>
                <li class="menu-title">📚 MANAJEMEN DATA</li>

                <li>
                    <a href="dashboard.php?page=buku" class="<?= $page == 'buku' ? 'active' : '' ?>">
                        Data Buku
                    </a>
                </li>

                <li>
                    <a href="dashboard.php?page=kategori" class="<?= $page == 'kategori' ? 'active' : '' ?>">
                        Data Kategori
                    </a>
                </li>

                <li>
                    <a href="dashboard.php?page=penerbit" class="<?= $page == 'penerbit' ? 'active' : '' ?>">
                        Data Penerbit
                    </a>
                </li>

                <li>
                    <a href="dashboard.php?page=peminjaman" class="<?= $page == 'peminjaman' ? 'active' : '' ?>">
                        Data Peminjaman
                    </a>
                </li>
            <?php endif; ?>

            <!-- ===== LAYANAN ===== -->
            <li class="menu-title">📖 LAYANAN</li>

            <li>
                <a href="dashboard.php?page=perpustakaan" class="<?= $page == 'perpustakaan' ? 'active' : '' ?>">
                    Perpustakaan
                </a>
            </li>

            <li>
                <a href="dashboard.php?page=koleksi_buku" class="<?= $page == 'koleksi_buku' ? 'active' : '' ?>">
                    Koleksi Buku
                </a>
            </li>

            <li>
                <a href="dashboard.php?page=pinjam" class="<?= $page == 'pinjam' ? 'active' : '' ?>">
                    Pinjam Buku
                </a>
            </li>

            <!-- ===== TRANSAKSI ===== -->
            <li class="menu-title">🔄 RIWAYAT</li>

            <li>
                <a href="dashboard.php?page=riwayat_peminjaman"
                    class="<?= $page == 'riwayat_peminjaman' ? 'active' : '' ?>">
                    Riwayat Peminjaman
                </a>
            </li>

            <li>
                <a href="dashboard.php?page=notifikasi" class="<?= $page == 'notifikasi' ? 'active' : '' ?>">
                    Notifikasi
                </a>
            </li>

            <!-- ===== AKUN ===== -->
            <li class="menu-title">⚙ AKUN</li>

            <li>
                <a href="dashboard.php?page=profile" class="<?= $page == 'profile' ? 'active' : '' ?>">
                    Profile
                </a>
            </li>

            <li>
                <a href="form_logout.php" onclick="return confirm('Yakin logout?')">
                    Logout
                </a>
            </li>

        </ul>
    </div>

    <!-- ===== CONTENT ===== -->
    <div class="content">
        <?php
        switch ($page) {

            case 'awal':
                include "awal.php";
                break;

            case "notifikasi":
                include "notifikasi/notifikasi.php";
                break;

            case "konfirmasi_pinjam":
                include "notifikasi/konfirmasi_pinjam_notifikasi.php";
                break;

            case "tolak_pinjam":
                include "notifikasi/tolak_pinjam_notifikasi.php";
                break;

            case "konfirmasi_kembali":
                include "notifikasi/konfirmasi_kembali_notifikasi.php";
                break;

            case "tolak_kembali":
                include "notifikasi/tolak_kembali_notifikasi.php";
                break;

            case 'perpustakaan':
                include "perpustakaan/perpustakaan_digital.php";
                break;

            case "detail_buku_invincible":
                include "perpustakaan/detail_buku/detail_buku_invincible.php";
                break;

            case "detail_buku_jjk":
                include "perpustakaan/detail_buku/detail_buku_jjk.php";
                break;

            case "detail_buku_csm":
                include "perpustakaan/detail_buku/detail_buku_csm.php";
                break;

            case 'koleksi_buku':
                include "perpustakaan/koleksi/koleksi_buku.php";
                break;

            case 'profile':
                include "profile.php";
                break;

            case 'buku':
                include "buku/view_buku.php";
                break;

            case 'tambah_buku':
                include "buku/form_tambah_buku.php";
                break;

            case 'simpan_buku':
                include "buku/simpan_buku.php";
                break;

            case 'edit_buku':
                include "buku/edit_buku.php";
                break;

            case 'simpan_edit_buku':
                include "buku/simpan_edit_buku.php";
                break;

            case 'hapus_buku':
                include "buku/hapus_buku.php";
                break;

            case 'kategori':
                include "kategori/view_kategori.php";
                break;

            case 'tambah_kategori':
                include "kategori/form_tambah_kategori.php";
                break;

            case 'simpan_kategori':
                include "kategori/simpan_kategori.php";
                break;

            case 'edit_kategori':
                include "kategori/edit_kategori.php";
                break;

            case 'simpan_edit_kategori':
                include "kategori/simpan_edit_kategori.php";
                break;

            case 'hapus_kategori':
                include "kategori/hapus_kategori.php";
                break;

            case 'penerbit':
                include "penerbit/view_penerbit.php";
                break;

            case 'tambah_penerbit':
                include "penerbit/form_tambah_penerbit.php";
                break;

            case 'simpan_penerbit':
                include "penerbit/simpan_penerbit.php";
                break;

            case 'edit_penerbit':
                include "penerbit/edit_penerbit.php";
                break;

            case 'simpan_edit_penerbit':
                include "penerbit/simpan_edit_penerbit.php";
                break;

            case 'hapus_penerbit':
                include "penerbit/hapus_penerbit.php";
                break;

            case 'peminjaman':
                include "peminjaman/view_pinjam.php";
                break;

            case 'pinjam':
                include "peminjaman/form_tambah_pinjam.php";
                break;

            case "riwayat_peminjaman":
                include "kembali/riwayat_peminjaman.php";
                break;

            case "proses_kembali":
                include "kembali/proses_kembali.php";
                break;

            case 'simpan_pinjam':
                include "peminjaman/simpan_pinjam.php";
                break;

            default:
                include "404.php";
                break;
        }
        ?>
    </div>

</body>

</html>