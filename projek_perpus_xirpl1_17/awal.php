<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Ambil role dari session bila tersedia, fallback ke $user jika ada
$role = $_SESSION['akses'] ?? ($user['akses'] ?? ''); // ambil dari kolom akses
// fungsi hitung data
function hitung($koneksi, $tabel){
    $tabel = preg_replace('/[^a-zA-Z0-9_]/', '', $tabel);
    $sql   = "SELECT COUNT(*) AS total FROM `$tabel`";
    $query = mysqli_query($koneksi, $sql);

    if($query){
        $data = mysqli_fetch_assoc($query);
        return (int) $data['total'];
    }
    return 0;
}

// total data
$totalBuku     = hitung($koneksi, "tbl_buku");
$totalKategori = hitung($koneksi, "tbl_kategori");
$totalPenerbit = hitung($koneksi, "tbl_penerbit");
$totalPinjam   = hitung($koneksi, "tbl_peminjaman");
?>

<style>

/* ================= GRID ================= */
.dashboard-cards{
    display:grid;
    grid-template-columns: repeat(auto-fit,minmax(260px,1fr));
    gap:35px;
    align-items: stretch;
}

/* ================= BASE CARD ================= */
.card{
    position: relative;
    display:flex;
    flex-direction:column;
    min-height:260px;
    padding:30px;
    border-radius:22px;
    transition: transform .3s ease, box-shadow .3s ease;
}

/* tombol selalu di bawah */
.card .btn{
    margin-top:auto;
    width:100%;
    text-align:center;
}

/* hover */
.card:hover{
    transform:translateY(-8px);
}

/* ================= ADMIN ================= */
.card-admin{
    background:linear-gradient(145deg,#fffdf9,#f8efe4);
    border:1px solid rgba(92,58,33,0.15);
    box-shadow:
        0 15px 35px rgba(92,58,33,0.12),
        inset 0 0 0 1px rgba(255,255,255,0.4);
}

.card-admin::before{
    content:"";
    position:absolute;
    top:0;
    left:0;
    width:100%;
    height:4px;
    background:linear-gradient(90deg,#5c3a21,#8b5e3c,#5c3a21);
}

.card-admin::after{
    content:"ADMIN PANEL";
    position:absolute;
    top:14px;
    right:18px;
    font-size:9px;
    letter-spacing:1px;
    padding:4px 8px;
    background:#5c3a21;
    color:#fff8ec;
    border-radius:20px;
    opacity:0.85;
}

/* ================= ANGGOTA ================= */
.card-anggota{
    background:#ffffff;
    border:1px solid #eee;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
}

.card-anggota::before{
    content:"";
    position:absolute;
    top:0;
    left:0;
    width:100%;
    height:4px;
    background: linear-gradient(90deg,#c58a5c,#e6b98a);
}

/* ================= TEXT ================= */
.card h3{
    font-size:13px;
    letter-spacing:1.2px;
    text-transform:uppercase;
    margin-bottom:18px;
    font-weight:600;
    color:#8b5e3c;
}

.card h2{
    font-size:42px;
    font-weight:700;
    margin:5px 0 25px;
    color:#4b2e2b;
}

.card p{
    font-size:14px;
    color:#6f4e37;
}

/* ================= BUTTON ================= */
.btn{
    display:inline-block;
    padding:12px;
    background:linear-gradient(135deg,#5c3a21,#8b5e3c);
    color:#fff8ec;
    border-radius:40px;
    font-size:13px;
    font-weight:600;
    text-decoration:none;
    letter-spacing:.5px;
    box-shadow:0 8px 20px rgba(92,58,33,0.25);
    transition:.3s;
}

.btn:hover{
    transform:translateY(-3px);
    box-shadow:0 15px 35px rgba(92,58,33,0.45);
}

</style>

<div class="container">
    <h2>Dashboard</h2>

    <p style="font-size:16px;">
        Selamat datang kembali,
        <b><?= htmlspecialchars($_SESSION['username'] ?? 'Pengguna'); ?></b> 👋
    </p>

    <hr style="margin:25px 0; border:1px solid #eee;">

    <div class="dashboard-cards">

    <!-- ================= ADMIN ================= -->
    <?php if ($role == 'admin'): ?>

        <div class="card card-admin">
            <h3>📚 Data Buku</h3>
            <h2><?= $totalBuku ?></h2>
            <a href="?page=buku" class="btn">Kelola</a>
        </div>

        <div class="card card-admin">
            <h3>🗂️ Data Kategori</h3>
            <h2><?= $totalKategori ?></h2>
            <a href="?page=kategori" class="btn">Kelola</a>
        </div>

        <div class="card card-admin">
            <h3>🏢 Data Penerbit</h3>
            <h2><?= $totalPenerbit ?></h2>
            <a href="?page=penerbit" class="btn">Kelola</a>
        </div>

        <div class="card card-admin">
            <h3>📖 Data Peminjaman</h3>
            <h2><?= $totalPinjam ?></h2>
            <a href="?page=peminjaman" class="btn">Kelola</a>
        </div>

    <?php endif; ?>

    <!-- ================= ANGGOTA ================= -->
    <?php if ($role == 'anggota'): ?>

    <?php
    $username = $_SESSION['username'] ?? '';
    $totalPinjamAktif = 0;
    $totalDenda = 0;

    if ($username !== '') {

        /* Pinjaman Aktif */
        $stmtAktif = mysqli_prepare($koneksi,
            "SELECT COUNT(*) AS total
             FROM tbl_peminjaman
             WHERE nama_peminjam = ?
             AND status IN ('dipinjamkan','Menunggu_Konfirmasi_Kembali')"
        );
        mysqli_stmt_bind_param($stmtAktif, "s", $username);
        mysqli_stmt_execute($stmtAktif);
        $resultAktif = mysqli_stmt_get_result($stmtAktif);
        $dataAktif = mysqli_fetch_assoc($resultAktif);
        $totalPinjamAktif = (int)$dataAktif['total'];

        /* Hitung Denda */
        $stmtDenda = mysqli_prepare($koneksi,
            "SELECT tanggal_kembali 
             FROM tbl_peminjaman 
             WHERE nama_peminjam = ? 
             AND tanggal_kembali IS NOT NULL 
             AND CURDATE() > tanggal_kembali"
        );
        mysqli_stmt_bind_param($stmtDenda, "s", $username);
        mysqli_stmt_execute($stmtDenda);
        $resultDenda = mysqli_stmt_get_result($stmtDenda);

        while($row = mysqli_fetch_assoc($resultDenda)){
            $hari = (int)((strtotime(date('Y-m-d')) - strtotime($row['tanggal_kembali'])) / 86400);

            if($hari > 3 && $hari <= 5) $denda = 2500;
            elseif($hari > 5 && $hari <= 7) $denda = 10000;
            elseif($hari > 7) $denda = 10000 * floor($hari/5);
            else $denda = 0;

            $totalDenda += $denda;
        }
    }
    ?>

        <div class="card card-anggota">
            <h3>📚 Perpustakaan</h3>
            <p>Lihat semua koleksi buku yang tersedia.</p>
            <a href="?page=perpustakaan" class="btn">Lihat Buku</a>
        </div>

        <div class="card card-anggota">
            <h3>📖 Pinjam Buku</h3>
            <h2><?= $totalPinjamAktif ?>/5</h2>

            <?php if ($totalPinjamAktif >= 5): ?>
                <p style="color:red;">Limit peminjaman tercapai!</p>
            <?php elseif ($totalDenda > 0): ?>
                <p style="color:red;">Tidak bisa pinjam karena ada denda!</p>
            <?php else: ?>
                <p>Masih bisa meminjam buku.</p>
                <a href="?page=pinjam" class="btn">Pinjam Sekarang</a>
            <?php endif; ?>
        </div>

        <div class="card card-anggota">
            <h3>📜 Riwayat Peminjaman</h3>
            <p>Lihat riwayat buku yang pernah dipinjam.</p>
            <a href="?page=riwayat_peminjaman" class="btn">Lihat Riwayat</a>
        </div>

        <div class="card card-anggota">
            <h3>💰 Denda</h3>
            <h2>Rp <?= number_format($totalDenda) ?></h2>
            <?php if ($totalDenda > 0): ?>
                <p style="color:red;">Anda harus melunasi denda terlebih dahulu.</p>
            <?php else: ?>
                <p>Tidak ada denda 🎉</p>
            <?php endif; ?>
        </div>

    <?php endif; ?>

    </div>
</div>